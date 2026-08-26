@extends('layouts.app')

@section('title', 'Edit User')
@section('header-title', 'Edit User')

@section('content')
<div class="max-w-lg bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Foto Profile</label>
            @if($user->foto_profile)
                <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="Foto {{ $user->name }}"
                    class="w-16 h-16 object-cover rounded border border-gray-300 mb-2">
            @endif
            <input type="file" name="foto_profile" accept="image/*"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @if($user->foto_profile)
                <p class="text-gray-500 text-xs mt-1">Biarkan kosong jika tidak ingin mengganti foto</p>
            @endif
            @error('foto_profile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Password Baru (kosongkan jika tidak diganti)</label>
            <input type="password" name="password" minlength="6"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Role</label>
            <select name="role" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                <option value="petugas" @selected(old('role', $user->role) === 'petugas')>Petugas</option>
                <option value="peminjam" @selected(old('role', $user->role) === 'peminjam')>Peminjam</option>
            </select>
            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">No HP</label>
            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
            <textarea name="alamat" rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('alamat', $user->alamat) }}</textarea>
        </div>

        <div class="flex items-center space-x-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Update
            </button>
            <a href="{{ route('admin.user.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection