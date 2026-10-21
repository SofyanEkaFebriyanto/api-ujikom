<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    // Halaman profil untuk semua role (admin, petugas, peminjam)
    public function edit()
    {
        $user = auth()->user();

        // tombol kembali mengikuti dashboard masing-masing role
        $backRoute = match ($user->role) {
            'admin' => route('admin.dashboard'),
            'petugas' => route('petugas.peminjaman.index'),
            default => route('peminjam.katalog'),
        };

        return view('profil', compact('user', 'backRoute'));
    }

    // Perbarui data diri: nama, no HP, alamat, foto profil
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $data = [
            'name' => $validated['name'],
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ];

        if ($request->hasFile('foto_profile')) {
            if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            $data['foto_profile'] = $request->file('foto_profile')->store('profile', 'public');
        }

        $user->update($data);

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    // Ganti password: wajib password saat ini yang benar + konfirmasi password baru
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profil.edit')->with('success', 'Password berhasil diganti.');
    }
}
