# Panduan Praktik — API Ujikom

Cheat sheet buat jalanin + ngembangin repo ini dari clone fresh (komputer lab, Windows + WSL + Docker).
Struktur: aplikasi Laravel ada di folder `backend/`, `docker-compose.yaml` ada di root repo ini.

---

## 1. Start dari nol (clone fresh)

Prasyarat: WSL2 + Ubuntu terinstall, Docker Desktop jalan (Settings → Resources → WSL
Integration → Ubuntu ON). Tes dulu: `docker ps` harus jalan tanpa error.

```bash
cd ~
git clone https://github.com/SofyanEkaFebriyanto/api-ujikom.git
cd api-ujikom
cp backend/.env.example backend/.env
```

Edit `backend/.env`, samakan blok DB dengan `docker-compose.yaml`:

```
DB_CONNECTION=mysql
DB_HOST=mysql-server
DB_PORT=3306
DB_DATABASE=API_ujikom
DB_USERNAME=api_ujikom
DB_PASSWORD=api_ujikom
```

Build + install + nyalakan (urutan penting: composer install SEBELUM `up`,
karena folder `backend/` di-mount dari host — vendor kosong = `artisan serve` langsung mati):

```bash
docker compose build
docker compose run --rm app-laravel composer install
docker compose up -d
```

Tunggu MySQL siap (~20–30 detik; cek `docker compose logs mysql-server` sampai
muncul "ready for connections"), lalu:

```bash
docker compose exec app-laravel php artisan key:generate
docker compose exec app-laravel php artisan migrate:fresh --seed
```

Alamat penting:

| Layanan    | URL                          | Keterangan                              |
|------------|------------------------------|-----------------------------------------|
| API        | http://localhost:8000        | endpoint di bawah `/api/...`           |
| phpMyAdmin | http://localhost:8081        | user `root`, password `passwordroot`    |
| MySQL host | localhost:3309               | dari container: `mysql-server:3306`     |

## 2. Akun seed buat testing

Semua password: `password123`

| Email              | Role     |
|--------------------|----------|
| admin@gmail.com    | admin    |
| petugas@gmail.com  | petugas  |
| rian@gmail.com     | peminjam |
| siti@gmail.com     | peminjam |
| eka@gmail.com      | peminjam |

Tes login cepat:

```bash
curl -X POST http://localhost:8000/api/login -H "Content-Type: application/json" \
  -d '{"email":"admin@gmail.com","password":"password123"}'
```

Harus balik token. Token itu dipakai sebagai header `Authorization: Bearer <token>`
buat endpoint yang butuh login.

## 3. Troubleshooting cepat

### "Please provide a valid cache path." (InvalidArgumentException)

Penyebab: folder `storage/framework/views` tidak ada. Laravel meng-compile template
Blade jadi PHP dan menyimpan hasilnya di folder itu. Path-nya dibaca pakai
`realpath()` di `config/view.php` — kalau foldernya tidak ada, `realpath()` mengembalikan
`false`, dan Blade Compiler (dibangun oleh ViewServiceProvider saat aplikasi boot)
melempar exception ini sebelum kode aplikasi jalan.

Dulu folder ini hilang saat clone karena Git tidak menyimpan folder kosong dan
`.gitignore` root mengabaikannya. Sekarang sudah diperbaiki (ada file placeholder
`.gitignore` di `views/`, `sessions/`, `logs/`). Kalau masih ketemu di clone lama:

```bash
docker compose exec app-laravel sh -c "mkdir -p storage/framework/views storage/framework/sessions storage/logs && chmod -R 777 storage bootstrap/cache"
```

Kalau masih error setelah itu, restart app sekali (cache realpath PHP bisa nyangkut):
`docker compose restart app-laravel`.

Penjelasan versi lisan buat pengawas: "Laravel meng-compile Blade ke PHP murni dan
menyimpan cache-nya di `storage/framework/views`. Path itu divalidasi saat aplikasi
boot; foldernya sempat hilang di hasil clone karena Git tidak me-version folder
kosong. Fix-nya folder dibuat ulang dan diberi izin tulis."

### SQLSTATE Connection refused / MySQL belum siap

Container app start lebih cepat dari MySQL. Tunggu 20–30 detik, cek
`docker compose logs mysql-server`, ulangi perintah artisan-nya. Bukan error kode.

### "No application encryption key has been specified"

`.env` belum punya `APP_KEY`. Jalankan:
`docker compose exec app-laravel php artisan key:generate`

### Port 8000 / 8081 / 3309 sudah dipakai

Matikan stack lain dulu: `docker compose down` di folder proyek lain, atau cek
`docker ps`. Jangan ganti port di compose kecuali terpaksa (dokumentasi ini asumsi port default).

### Permission denied di storage / bootstrap/cache

```bash
docker compose exec app-laravel sh -c "chmod -R 777 storage bootstrap/cache"
```

### Database kotor / migrate error tabel sudah ada

```bash
docker compose exec app-laravel php artisan migrate:fresh --seed
```

## 4. Pola nambah fitur (konvensi repo ini)

Ikuti urutan ini, contoh modul yang sudah ada: Alat, Kategori, Peminjaman.

1. **Migration** (kalau ada tabel/kolom baru):
   `docker compose exec app-laravel php artisan make:migration <nama>` →
   isi `up()`/`down()` → `php artisan migrate`.
2. **Model** di `app/Models/` — `$fillable`, relasi (`belongsTo`/`hasMany`), cast bila perlu.
3. **Form Request** di `app/Http/Requests/<Modul>/` — pisah `Store<X>Request` dan
   `Update<X>Request`, isi `rules()` + `authorize()` return `true`.
4. **Resource** di `app/Http/Resources/` — tentukan field yang keluar ke API, jangan
   lempar model mentah.
5. **Controller** di `app/Http/Controllers/API/` — pakai Form Request + Resource,
   response JSON konsisten: `{"message": ..., "data": ...}`.
6. **Route** di `routes/api.php` — daftarkan di dalam grup `auth:sanctum`, lalu kunci
   per role pakai middleware alias yang sudah terdaftar di `bootstrap/app.php`:
   `role.admin`, `role.petugas`, `role.peminjam`, atau `role` (CheckRole, terima
   parameter, mis. `role:admin,petugas`).
7. **Tes per role**: login sebagai tiap role, endpoint yang boleh harus 200,
   yang tidak boleh harus 403 dengan pesan sesuai role-nya.
8. **Log aktivitas** (kalau fitur menyentuh peminjaman/pengembalian): modul yang ada
   mencatat ke `log_aktivitas` lewat Observer — cek `app/Observers/` sebelum bikin cara baru.

## 5. Matriks akses (sesuai modul/PDF)

| Endpoint                  | admin | petugas | peminjam        |
|---------------------------|:-----:|:-------:|-----------------|
| kelola user               |  ✓    |         |                 |
| kelola kategori           |  ✓    |         |                 |
| kelola alat               |  ✓    |         |                 |
| lihat katalog alat        |  ✓    |   ✓     | ✓               |
| ajukan peminjaman         |       |         | ✓               |
| approval peminjaman       |       |   ✓     |                 |
| pengembalian              |       |   ✓     |                 |
| laporan peminjaman (PDF)  |  ✓    |   ✓     |                 |
| log aktivitas             |  ✓    |   ✓     |                 |
| lihat peminjaman sendiri  |       |         | ✓ (miliknya saja)|

Detail otoritatifnya ada di `routes/api.php` — kalau ragu, baca route-nya, jangan tebak.

## 6. Hal yang sering jadi jebakan

- `IsPeminjam` memeriksa role `'peminjam'`. Di PDF ada salah ketik yang menyebut
  role lain — ikuti kode + logika, bukan typo PDF.
- Pesan error 403 dari middleware role bunyinya spesifik per role. Jangan diganti
  generik, itu bagian dari spek.
- Status `ditolak` pada peminjaman datang dari migration terpisah (enum ditambah
  belakangan). Kalau bikin migration status baru, pola yang sama: `ALTER` enum,
  jangan edit migration awal.
- Laporan peminjaman bisa diakses admin DAN petugas (bukan admin saja).
- Peminjam hanya boleh melihat data miliknya sendiri — selalu filter `where user_id = auth()->id()`.
- Alias middleware didaftarkan di `bootstrap/app.php`. Kalau bikin middleware baru,
  daftarkan di sana, bukan di Kernel (Laravel 12 tidak pakai Kernel.php).

## 7. Command harian yang sering dipakai

```bash
docker compose up -d              # nyalakan semua
docker compose down               # matikan (data MySQL aman di volume)
docker compose ps                 # status container
docker compose logs app-laravel   # log aplikasi
docker compose exec app-laravel php artisan route:list   # daftar semua route
docker compose exec app-laravel php artisan test         # jalanin test
```
