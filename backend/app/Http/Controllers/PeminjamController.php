<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetilPinjam;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PeminjamController extends Controller
{
    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();
        return view('peminjam.katalog', compact('alats'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => 'required|date|after:today',
            'alat_id' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // Buat master Peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            // Masukkan daftar alat yang dipinjam ke detail_pinjam
            foreach ($request->alat_id as $index => $alatId) {
                DetilPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Mengajukan peminjaman #' . $peminjaman->id,
            ]);

            DB::commit();
            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil diajukan.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjamans'));
    }

    // Peminjam mengajukan pengembalian alat
    // CATATAN DESAIN: kolom pengembalian.petugas_id NOT NULL sehingga tidak bisa
    // membuat record pengembalian tanpa petugas. Tanpa migration baru, aksi ini
    // hanya memverifikasi kepemilikan peminjaman lalu memberi tahu bahwa
    // pengembalian diproses oleh petugas (fallback).
    public function ajukanPengembalian($id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', auth()->id()) // guard IDOR manual
            ->firstOrFail();

        if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
            return redirect()->route('peminjam.riwayat')->with('error', 'Peminjaman ini tidak sedang dalam status dipinjam.');
        }

        return redirect()->route('peminjam.riwayat')->with('success', 'Permintaan diterima. Pengembalian diproses oleh petugas, silakan datang ke loket dengan alat.');
    }
}
