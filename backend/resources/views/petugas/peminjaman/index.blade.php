@extends('layouts.app')

@section('title', 'Kelola Peminjaman')
@section('header-title', 'Daftar Pengajuan Peminjaman')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                    <th class="py-3 px-4 border-b">Rencana Kembali</th>
                    <th class="py-3 px-4 border-b">Detail Alat</th>
                    <th class="py-3 px-4 border-b">Status</th>
                    <th class="py-3 px-4 border-b w-56">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($peminjamans as $peminjaman)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $peminjaman->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">{{ $peminjaman->tgl_pinjam }}</td>
                        <td class="py-3 px-4 border-b">{{ $peminjaman->tgl_kembali_plan }}</td>
                        <td class="py-3 px-4 border-b">
                            @foreach($peminjaman->detailPinjam as $detail)
                                <div>{{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }}x)</div>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                @if($peminjaman->status === 'diajukan') bg-amber-100 text-amber-700
                                @elseif($peminjaman->status === 'dipinjam') bg-blue-100 text-blue-700
                                @elseif($peminjaman->status === 'telat') bg-red-100 text-red-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                {{ $peminjaman->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b">
                            <div class="flex items-center space-x-2 whitespace-nowrap">
                                @if($peminjaman->status === 'diajukan')
                                    <form action="{{ route('petugas.peminjaman.setujui', $peminjaman->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                            Setujui
                                        </button>
                                    </form>
                                @endif
                                @if($peminjaman->status === 'dipinjam' && !$peminjaman->pengembalian)
                                    <button type="button" onclick="document.getElementById('form-kembali-{{ $peminjaman->id }}').classList.toggle('hidden')"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Proses Pengembalian
                                    </button>
                                @endif
                                @if(in_array($peminjaman->status, ['dikembalikan']) || $peminjaman->pengembalian)
                                    <div class="text-xs">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-700">Dikembalikan</span>
                                        @if($peminjaman->pengembalian)
                                            @if(!is_null($peminjaman->pengembalian->denda) && (int) $peminjaman->pengembalian->denda > 0)
                                                <div class="mt-1 text-red-600">Denda: Rp{{ number_format($peminjaman->pengembalian->denda, 0, ',', '.') }}</div>
                                            @else
                                                <div class="mt-1 text-gray-500">Tanpa denda</div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @if($peminjaman->status === 'dipinjam' && !$peminjaman->pengembalian)
                                <form id="form-kembali-{{ $peminjaman->id }}" action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST" class="hidden mt-2 space-y-2 border-t pt-2">
                                    @csrf
                                    <label class="block text-xs font-semibold text-gray-600">Kondisi Kembali</label>
                                    <select name="kondisi_kembali" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded" required>
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                        <option value="Rusak Berat">Rusak Berat</option>
                                    </select>
                                    <label class="block text-xs font-semibold text-gray-600">Denda</label>
                                    <input type="number" name="denda" min="0" value="0"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Simpan Pengembalian
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection