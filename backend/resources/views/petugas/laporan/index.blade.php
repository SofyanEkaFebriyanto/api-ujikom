@extends('layouts.app')

@section('title', 'Laporan Peminjaman')
@section('header-title', 'Laporan Peminjaman')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
        <ul class="list-disc pl-5 mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200">
        <!-- Form Filter -->
        <form action="{{ route('petugas.laporan.index') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Awal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    @foreach(['diajukan', 'dipinjam', 'dikembalikan', 'ditolak', 'telat'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg transition">
                    Filter
                </button>
                @if(request()->anyFilled(['start_date', 'end_date', 'status']))
                    <a href="{{ route('petugas.laporan.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                    <button type="submit" name="cetak" value="pdf" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm font-semibold rounded-lg transition whitespace-nowrap">
                        Cetak PDF
                    </button>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b w-16 text-center">No</th>
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                    <th class="py-3 px-4 border-b">Rencana Kembali</th>
                    <th class="py-3 px-4 border-b">Detail Alat</th>
                    <th class="py-3 px-4 border-b">Status</th>
                    <th class="py-3 px-4 border-b">Denda</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($laporans as $index => $laporan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b text-center">{{ $laporans->firstItem() + $index }}</td>
                        <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $laporan->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">{{ $laporan->tgl_pinjam }}</td>
                        <td class="py-3 px-4 border-b">{{ $laporan->tgl_kembali_plan }}</td>
                        <td class="py-3 px-4 border-b">
                            @foreach($laporan->detailPinjam as $detail)
                                <div>{{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }}x)</div>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                @if($laporan->status === 'diajukan') bg-amber-100 text-amber-700
                                @elseif($laporan->status === 'dipinjam') bg-blue-100 text-blue-700
                                @elseif($laporan->status === 'telat') bg-red-100 text-red-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                {{ $laporan->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b">Rp {{ number_format($laporan->pengembalian->denda ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-gray-500">Belum ada data laporan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $laporans->links() }}
    </div>
</div>
@endsection
