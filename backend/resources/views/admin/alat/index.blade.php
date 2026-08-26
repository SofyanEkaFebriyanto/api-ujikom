@extends('layouts.app')

@section('title', 'Kelola Alat')
@section('header-title', 'Daftar Alat')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <div></div>
        <a href="{{ route('admin.alat.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
            + Tambah Alat
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Nama Alat</th>
                    <th class="py-3 px-4 border-b">Kategori</th>
                    <th class="py-3 px-4 border-b">Stok</th>
                    <th class="py-3 px-4 border-b">Status Kondisi</th>
                    <th class="py-3 px-4 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($alats as $alat)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $alat->nama_alat }}</td>
                        <td class="py-3 px-4 border-b">{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">{{ $alat->stok }}</td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                @if($alat->status_kondisi === 'baik') bg-emerald-100 text-emerald-700
                                @elseif($alat->status_kondisi === 'rusak') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $alat->status_kondisi }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b space-x-2">
                            <a href="{{ route('admin.alat.edit', $alat->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Edit</a>
                            <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus alat ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data alat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

</parameter>