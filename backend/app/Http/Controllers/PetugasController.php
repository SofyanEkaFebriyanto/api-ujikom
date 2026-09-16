<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])->latest()->get();
        return view('petugas.peminjaman.index', compact('peminjamans'));
    }

    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menyetujui peminjaman #' . $peminjaman->id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            // Update status peminjaman jadi selesai
            $peminjaman->update(['status' => 'dikembalikan']);

            // Kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Memproses pengembalian peminjaman #' . $peminjaman->id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Laporan: filter rentang tanggal + status (data + cetak PDF)
    public function indexLaporan(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            'status' => 'nullable|in:diajukan,dipinjam,dikembalikan,ditolak,telat',
        ]);

        $query = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian.petugas']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // cetak PDF dari hasil filter yang sama
        if ($request->input('cetak') === 'pdf') {
            $laporans = $query->latest()->get();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Mencetak laporan peminjaman (PDF).',
            ]);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('petugas.laporan.cetak', [
                'laporans' => $laporans,
                'filters' => $request->only(['start_date', 'end_date', 'status']),
            ])->setPaper('a4', 'landscape');

            return $pdf->download('laporan-peminjaman-' . now()->format('Ymd-His') . '.pdf');
        }

        $laporans = $query->latest()->paginate(10)->withQueryString();

        return view('petugas.laporan.index', compact('laporans'));
    }
}
