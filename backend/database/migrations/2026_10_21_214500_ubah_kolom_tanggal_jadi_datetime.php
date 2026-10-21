<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Koreksi pengawas: jam pada waktu pinjam, rencana kembali, dan waktu
     * pengembalian harus tampil. Kolomnya semula bertipe DATE sehingga jam
     * tidak pernah tersimpan; diubah menjadi DATETIME.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dateTime('tgl_pinjam')->change();
            $table->dateTime('tgl_kembali_plan')->change();
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dateTime('tgl_kembali')->change();
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->date('tgl_pinjam')->change();
            $table->date('tgl_kembali_plan')->change();
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->date('tgl_kembali')->change();
        });
    }
};
