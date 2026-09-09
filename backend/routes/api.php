<?php

use App\Http\Controllers\API\AlatController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\LaporanController;
use App\Http\Controllers\API\LogAktivitasController;
use App\Http\Controllers\API\PeminjamanController;
use App\Http\Controllers\API\PengembalianController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// Public Routes (Tidak perlu token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Wajib membawa Bearer Token dari Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Modul mendaftarkan endpoint ini di dua blok role sekaligus.
    // Didaftarkan sekali dengan CheckRole multi-role agar tidak tertutup route ganda:
    // - katalog: blok admin + peminjam
    // - approve: blok admin + petugas
    // - laporan-peminjaman: blok admin + petugas
    Route::middleware('role:admin,peminjam')->group(function () {
        Route::get('/katalog', [AlatController::class, 'katalog']);
    });

    Route::middleware('role:admin,petugas')->group(function () {
        Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
        Route::get('/laporan-peminjaman', [LaporanController::class, 'index']);
    });

    Route::middleware('role.admin')->group(function () {
        Route::apiResource('kategori', KategoriController::class);
        Route::apiResource('alat', AlatController::class);
        Route::apiResource('users', UserController::class);

        Route::get('/peminjaman', [PeminjamanController::class, 'index']);
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
        Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update']);
        Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);

        Route::get('/pengembalian', [PengembalianController::class, 'index']);
        Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);
        Route::put('/pengembalian/{pengembalian}', [PengembalianController::class, 'update']);
        Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);

        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);
    });

    Route::middleware('role.petugas')->group(function () {
        Route::post('/pengembalian', [PengembalianController::class, 'store']);
    });

    Route::middleware('role.peminjam')->group(function () {
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);
        Route::get('/riwayat-pinjam', [PeminjamanController::class, 'riwayat']);
    });
});
