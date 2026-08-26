@extends('layouts.app')

@section('title', 'Kelola Pengembalian')
@section('header-title', 'Daftar Pengembalian')

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
                    <th class="py-3 px-4 border-b">#</th>
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Tgl Kembali</th>
                    <th class="py-3 px-4 border-b">Kondisi</th>
                    <th class="py-3 px-4 border-b">Denda</th>
                    <th class="py-3 px-4 border-b">Petugas</th>
                    <th class="py-3 px-4 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($pengembalians as $peng)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b">{{ $peng->id }}</td>
                        <td class="py-3 px-4 border-b font-medium text-gray-900">
                            {{ $peng->peminjaman->user->name ?? '-' }} (Pinjam #{{ $peng->peminjaman_id }})
                        </td>
                        <td class="py-3 px-4 border-b">{{ $peng->tgl_kembali?->format('Y-m-d') }}</td>
                        <td class="py-3 px-4 border-b">
                            <form action="{{ route('admin.pengembalian.update', $peng->id) }}" method="POST" class="flex items-center gap-1" onsubmit="return confirm('Perbarui data pengembalian ini?')">
                                @csrf @method('PUT')
                                <select name="kondisi_kembali" class="border rounded px-2 py-1 text-xs">
                                    @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kondisi)
                                        <option value="{{ $kondisi }}" @selected($peng->kondisi_kembali === $kondisi)>{{ $kondisi }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="denda" value="{{ $peng->denda }}" min="0" class="border rounded px-2 py-1 text-xs w-20" title="Denda">
                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Simpan</button>
                            </form>
                        </td>
                        <td class="py-3 px-4 border-b">Rp {{ number_format($peng->denda, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 border-b">{{ $peng->petugas->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">
                            <form action="{{ route('admin.pengembalian.destroy', $peng->id) }}" method="POST" onsubmit="return confirm('Hapus pengembalian ini? Status peminjaman kembali menjadi dipinjam dan stok dikurangi lagi.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-gray-500">Belum ada data pengembalian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-200">
        {{ $pengembalians->links() }}
    </div>
</div>
@endsection
