@extends('layouts.app')

@section('title', 'Kelola Peminjaman')
@section('header-title', 'Daftar Peminjaman')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <div></div>
        <a href="{{ route('admin.peminjaman.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
            + Buat Peminjaman
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">#</th>
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                    <th class="py-3 px-4 border-b">Tgl Kembali Plan</th>
                    <th class="py-3 px-4 border-b">Status</th>
                    <th class="py-3 px-4 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($peminjamans as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b">{{ $p->id }}</td>
                        <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $p->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">{{ $p->tgl_pinjam?->format('Y-m-d') }}</td>
                        <td class="py-3 px-4 border-b">{{ $p->tgl_kembali_plan?->format('Y-m-d') }}</td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                @if($p->status === 'dipinjam') bg-blue-100 text-blue-700
                                @elseif($p->status === 'dikembalikan') bg-emerald-100 text-emerald-700
                                @elseif($p->status === 'diajukan') bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b space-x-2">
                            <form action="{{ route('admin.peminjaman.updateStatus', $p->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-xs">
                                    <option value="">Ubah Status</option>
                                    <option value="diajukan">Diajukan</option>
                                    <option value="dipinjam">Dipinjam</option>
                                    <option value="dikembalikan">Dikembalikan</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </form>
                            <form action="{{ route('admin.peminjaman.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus peminjaman ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
                            </form>
                            @if(in_array($p->status, ['dipinjam', 'telat']) && !$p->pengembalian)
                                <form action="{{ route('admin.pengembalian.store', $p->id) }}" method="POST" class="inline-flex items-center gap-1" onsubmit="return confirm('Catat pengembalian untuk peminjaman #{{ $p->id }}?')">
                                    @csrf
                                    <select name="kondisi_kembali" class="border rounded px-2 py-1 text-xs">
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                        <option value="Rusak Berat">Rusak Berat</option>
                                    </select>
                                    <input type="number" name="denda" value="0" min="0" class="border rounded px-2 py-1 text-xs w-16" title="Denda">
                                    <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold">Kembalikan</button>
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

    <div class="p-4 border-t border-gray-200">
        {{ $peminjamans->links() }}
    </div>
</div>
@endsection
