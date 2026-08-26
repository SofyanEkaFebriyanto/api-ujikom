@extends('layouts.app')

@section('title', 'Buat Peminjaman')
@section('header-title', 'Buat Peminjaman Baru')

@section('content')
@if(session('error'))
    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl">
    <form action="{{ route('admin.peminjaman.store') }}" method="POST" id="form-peminjaman">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Peminjam (User)</label>
                <select name="user_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    <option value="">-- Pilih Peminjam --</option>
                    @foreach(\App\Models\User::where('role', 'peminjam')->get() as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rencana Kembali</label>
                <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan') }}" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                @error('tgl_kembali_plan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Daftar Alat</label>
                <div id="alat-rows" class="space-y-2">
                    <div class="flex gap-2 items-center alat-row">
                        <select name="alat_id[]" required class="flex-1 border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                            <option value="">-- Pilih Alat --</option>
                            @foreach($alats as $a)
                                <option value="{{ $a->id }}">{{ $a->nama_alat }} (Stok: {{ $a->stok }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="jumlah[]" min="1" value="1" required placeholder="Jml"
                            class="w-20 border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2">&times;</button>
                    </div>
                </div>
                <button type="button" onclick="addAlatRow()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Tambah Alat</button>
                @error('alat_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg transition">Simpan Peminjaman</button>
                <a href="{{ route('admin.peminjaman.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-5 py-2 rounded-lg transition">Batal</a>
            </div>
        </div>
    </form>
</div>

<script>
function addAlatRow() {
    const container = document.getElementById('alat-rows');
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-center alat-row';
    row.innerHTML = `
        <select name="alat_id[]" required class="flex-1 border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
            <option value="">-- Pilih Alat --</option>
            @foreach($alats as $a)
                <option value="{{ $a->id }}">{{ $a->nama_alat }} (Stok: {{ $a->stok }})</option>
            @endforeach
        </select>
        <input type="number" name="jumlah[]" min="1" value="1" required placeholder="Jml"
            class="w-20 border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2">&times;</button>
    `;
    container.appendChild(row);
}
</script>
@endsection

</parameter>