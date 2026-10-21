<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $peminjaman = [
            [
                'user_id' => 3,
                'tgl_pinjam' => '2026-06-01 08:00:00',
                'tgl_kembali_plan' => '2026-06-04 15:00:00',
                'status' => 'dikembalikan',
            ],
            [
                'user_id' => 4,
                'tgl_pinjam' => '2026-06-02 09:30:00',
                'tgl_kembali_plan' => '2026-06-05 14:00:00',
                'status' => 'dikembalikan',
            ],
            [
                'user_id' => 5,
                'tgl_pinjam' => '2026-06-03 10:15:00',
                'tgl_kembali_plan' => '2026-06-06 13:00:00',
                'status' => 'telat',
            ],
            [
                'user_id' => 3,
                'tgl_pinjam' => '2026-06-08 07:45:00',
                'tgl_kembali_plan' => '2026-06-11 15:30:00',
                'status' => 'dipinjam',
            ],
            [
                'user_id' => 4,
                'tgl_pinjam' => '2026-06-09 11:20:00',
                'tgl_kembali_plan' => '2026-06-12 14:30:00',
                'status' => 'diajukan',
            ],
        ];

        foreach ($peminjaman as $pinjam) {
            Peminjaman::create($pinjam);
        }
    }
}
