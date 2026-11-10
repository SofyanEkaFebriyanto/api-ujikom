# Aplikasi Peminjaman Alat

Aplikasi web dan API untuk peminjaman alat. Dibangun dengan Laravel 12 dan MySQL, dijalankan dengan Docker. Proyek ini disiapkan sebagai bahan Evaluasi Pra-Ujikom (gladi bersih UKK).

## Fitur

- Autentikasi: register, login, logout, dan profil pengguna (token Sanctum)
- Peran pengguna: Admin, Petugas, dan Peminjam
- CRUD Kategori, Alat, dan User (khusus Admin)
- Katalog alat untuk Peminjam
- Peminjaman alat, persetujuan oleh Petugas, dan pengembalian alat
- Laporan peminjaman dan log aktivitas

## Yang dibutuhkan

- Docker dan Docker Compose
- Git

## Cara menjalankan (dari clone baru)

1. Clone repo ini:

   git clone https://github.com/SofyanEkaFebriyanto/api-ujikom.git
   cd api-ujikom

2. Salin file environment:

   cp backend/.env.example backend/.env

3. Install dependency PHP dulu, sebelum container dinyalakan:

   cd backend
   composer install
   cd ..

   Langkah ini wajib, karena folder vendor tidak ikut masuk repo dan akan tertutup oleh bind mount kalau container langsung dinyalakan.

4. Nyalakan semua container:

   docker compose up -d --build

5. Generate application key:

   docker compose exec laravel-api php artisan key:generate

6. Jalankan migrasi dan seeder:

   docker compose exec laravel-api php artisan migrate:fresh --seed

7. Buka http://localhost:8000

## Layanan yang berjalan

- Laravel (aplikasi): http://localhost:8000
- phpMyAdmin: http://localhost:8081
- MySQL: localhost:3309

## Akun bawaan

Seeder menyediakan akun untuk tiga peran (password lihat di database/seeders/UserSeeder.php):

- Admin: admin@gmail.com
- Petugas: petugas@gmail.com
- Peminjam: rian@gmail.com, siti@gmail.com, eka@gmail.com

## Catatan

- Foto alat disimpan di backend/storage/app/public dan disajikan lewat bind mount di docker-compose.yaml, jadi perintah php artisan storage:link tidak diperlukan.
- File .env, vendor, dan node_modules tidak ikut masuk repo.
