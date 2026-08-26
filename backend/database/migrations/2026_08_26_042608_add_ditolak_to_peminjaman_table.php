<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('status', ['diajukan', 'dipinjam', 'dikembalikan', 'ditolak', 'telat'])
                ->default('diajukan')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('status', ['diajukan', 'dipinjam', 'dikembalikan', 'telat'])
                ->default('diajukan')
                ->change();
        });
    }
};
