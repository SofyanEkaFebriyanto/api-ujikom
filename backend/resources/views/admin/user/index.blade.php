@extends('layouts.app')

@section('title', 'Kelola User')
@section('header-title', 'Manajemen User')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">

        <!-- Form Search -->
        <form action="{{ route('admin.user.index') }}" method="GET" class="flex w-full sm:w-80">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.user.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                    Reset
                </a>
            @endif
        </form>

        <!-- Tombol Tambah -->
        <a href="{{ route('admin.user.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
            + Tambah User
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b w-16 text-center">No</th>
                    <th class="py-3 px-4 border-b">Nama</th>
                    <th class="py-3 px-4 border-b">Email</th>
                    <th class="py-3 px-4 border-b">Role</th>
                    <th class="py-3 px-4 border-b">No HP</th>
                    <th class="py-3 px-4 border-b w-48">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b text-center">{{ $users->firstItem() + $index }}</td>
                        <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="py-3 px-4 border-b">{{ $user->email }}</td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                @if($user->role === 'admin') bg-gray-800 text-white
                                @elseif($user->role === 'petugas') bg-blue-100 text-blue-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b">{{ $user->no_hp ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $users->links() }}
    </div>
</div>
@endsection