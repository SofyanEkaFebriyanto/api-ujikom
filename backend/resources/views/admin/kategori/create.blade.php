@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('header-title', 'Tambah Kategori Baru')

@section('content')
<div class="max-w-lg bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.kategori.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Kategori</label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('nama_kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center space-x-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Simpan
            </button>
            <a href="{{ route('admin.kategori.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection