@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('header-title', 'Detail Peminjaman #' . $peminjaman->id)

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl space-y-6">
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div><span class="font-semibold text-gray-600">Peminjam:</span> {{ $peminjaman->user->name ?? '-' }}</div>
        <div><span class="font-semibold text-gray-600">Email:</span> {{ $peminjaman->user->email ?? '-' }}</div>
        <div><span class="font-semibold text-gray-600">Tgl Pinjam:</span> {{ $peminjaman->tgl_pinjam?->format('Y-m-d') }}</div>
        <div><span class="font-semibold text-gray-600">Tgl Rencana Kembali:</span> {{ $peminjaman->tgl_kembali_plan?->format('Y-m-d') }}</div>
        <div>
            <span class="font-semibold text-gray-600">Status:</span>
            <span class="px-2 py-0.5 rounded text-xs font-semibold
                @if($peminjaman->status === 'dipinjam') bg-blue-100 text-blue-700
                @elseif($peminjaman->status === 'dikembalikan') bg-emerald-100 text-emerald-700
                @elseif($peminjaman->status === 'diajukan') bg-amber-100 text-amber-700
                @else bg-red-100 text-red-700 @endif">
                {{ $peminjaman->status }}
            </span>
        </div>
    </div>

    <div>
        <h3 class="font-semibold text-gray-800 mb-2">Daftar Alat Dipinjam</h3>
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase tracking-wider">
                    <th class="py-2 px-3 border-b">Alat</th>
                    <th class="py-2 px-3 border-b">Jumlah</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach($peminjaman->detailPinjam as $detail)
                    <tr>
                        <td class="py-2 px-3 border-b">{{ $detail->alat->nama_alat ?? '-' }}</td>
                        <td class="py-2 px-3 border-b">{{ $detail->jumlah }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($peminjaman->pengembalian)
    <div class="border-t pt-4">
        <h3 class="font-semibold text-gray-800 mb-2">Data Pengembalian</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-semibold text-gray-600">Tgl Kembali:</span> {{ $peminjaman->pengembalian->tgl_kembali?->format('Y-m-d') }}</div>
            <div><span class="font-semibold text-gray-600">Kondisi:</span> {{ $peminjaman->pengembalian->kondisi_kembali }}</div>
            <div><span class="font-semibold text-gray-600">Denda:</span> Rp {{ number_format($peminjaman->pengembalian->denda, 0, ',', '.') }}</div>
            <div><span class="font-semibold text-gray-600">Petugas:</span> {{ $peminjaman->pengembalian->petugas->name ?? '-' }}</div>
        </div>
    </div>
    @endif

    <div class="pt-2">
        <a href="{{ route('admin.peminjaman.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-5 py-2 rounded-lg transition text-sm">Kembali</a>
    </div>
</div>
@endsection

</parameter>