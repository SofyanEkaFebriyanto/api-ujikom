@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('header-title', 'Log Aktivitas Sistem')

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
        <form action="{{ route('admin.log.index') }}" method="GET" class="flex w-full sm:w-80">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas / user..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.log.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                    Reset
                </a>
            @endif
        </form>

        <div class="text-sm text-gray-500 whitespace-nowrap">
            Total: {{ $logs->total() }} aktivitas
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b w-16 text-center">No</th>
                    <th class="py-3 px-4 border-b">Waktu</th>
                    <th class="py-3 px-4 border-b">User</th>
                    <th class="py-3 px-4 border-b">Role</th>
                    <th class="py-3 px-4 border-b">Aktivitas</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($logs as $index => $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b text-center">{{ $logs->firstItem() + $index }}</td>
                        <td class="py-3 px-4 border-b whitespace-nowrap">{{ $log->created_at }}</td>
                        <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                @if(($log->user->role ?? '') === 'admin') bg-gray-800 text-white
                                @elseif(($log->user->role ?? '') === 'petugas') bg-blue-100 text-blue-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                {{ $log->user->role ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b">{{ $log->aktivitas }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Belum ada log aktivitas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $logs->links() }}
    </div>
</div>
@endsection
