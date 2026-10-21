# Catatan Koreksi Pengawas (21 Okt 2026)

Semua poin koreksi sudah diperbaiki dan terverifikasi jalan (register, pengajuan,
profil, cetak laporan, dan API dites satu per satu). File ini ringkasannya:
masalahnya apa, diubah di mana, cara demonya, dan jawaban satu kalimat kalau
pengawas bertanya. Flow menjalankan proyek tetap ikuti PANDUAN-PRAKTIK.md.

Dua hal yang perlu diingat sebelum demo:

1. Migrasi baru ikut jalan otomatis saat `migrate:fresh --seed`, tidak ada
   langkah tambahan.
2. Foto dan gambar tampil lewat symlink `public/storage`. Di Docker symlink
   itu sudah ada di repo (mengarah ke /var/www/storage/app/public). Kalau
   menjalankan di luar Docker dan gambar tidak muncul, jalankan sekali:
   `php artisan storage:link`.

## 1. Timestamp bukan zona WIB

- Masalah: timezone aplikasi memang sudah Asia/Jakarta, tapi server MySQL di
  Docker masih UTC, jadi jam yang terbaca di database bisa selisih 7 jam.
- Perbaikan: timezone diset di tiga lapis. `APP_TIMEZONE=Asia/Jakarta` di
  `.env.example` dan `config/app.php`, sesi MySQL `+07:00` di
  `config/database.php`, dan server MySQL di `docker-compose.yaml`
  (`--default-time-zone=+07:00`, `TZ: Asia/Jakarta`).
- Jawaban singkat: "Kemarin yang masih UTC itu server MySQL-nya, sekarang
  aplikasi dan database sama-sama WIB."

## 2. Jam di waktu pinjam dan rencana kembali tidak tampil

- Masalah: kolom `tgl_pinjam`, `tgl_kembali_plan`, dan `tgl_kembali` bertipe
  DATE, jadi jam memang tidak pernah tersimpan.
- Perbaikan: migrasi baru mengubah ketiganya menjadi DATETIME. Form pengajuan
  (katalog peminjam dan tambah peminjaman admin) memakai input
  `datetime-local`. API tidak lagi memotong jam (`now()` utuh, resource
  memformat `Y-m-d H:i:s`). Semua tampilan (riwayat, kelola peminjaman,
  pengembalian, laporan) memformat `d-m-Y H:i`. Data seeder juga diberi jam
  agar contohnya langsung terlihat.
- Demo: login peminjam, ajukan peminjaman, isi rencana kembali beserta jamnya,
  buka Riwayat: tanggal dan jam tampil.
- Jawaban singkat: "Kolomnya kemarin DATE, sekarang DATETIME, jadi jam ikut
  tersimpan dan tampil."

## 3. Foto alat di dashboard peminjam

- Masalah: kolom `gambar` dan upload di admin sebenarnya sudah ada, tapi
  katalog peminjam tidak pernah menampilkannya.
- Perbaikan: katalog peminjam sekarang punya kolom Foto. Alat tanpa file foto
  menampilkan teks "Tidak ada foto" (bukan gambar rusak). API katalog juga
  mengembalikan URL gambar.
- Demo: admin tambah alat dengan gambar, login peminjam, foto muncul di
  katalog.
- Jawaban singkat: "Fotonya kemarin berhenti di admin, sekarang diteruskan
  tampil ke katalog peminjam."

## 4. Foto user di kelola user admin

- Masalah: upload foto user sudah ada di form tambah/edit, tapi daftar user
  tidak menampilkan foto.
- Perbaikan: tabel Kelola User sekarang punya kolom Foto (thumbnail; user
  tanpa foto tampil inisial namanya).
- Demo: admin tambah user dengan foto, foto langsung terlihat di daftar.
- Jawaban singkat: "Upload-nya sudah ada, yang ditambah tampilan fotonya di
  daftar user."

## 5. Halaman profil untuk masing-masing user

- Fitur baru: halaman Profil untuk semua role (admin, petugas, peminjam).
  Bisa ubah nama, no HP, alamat, dan foto profil. Ada form ganti password
  terpisah yang mewajibkan password saat ini dan konfirmasi password baru.
- Akses: tombol Profil di header panel admin/petugas dan di navbar peminjam.
  Route: `GET /profil`, `PUT /profil`, `PUT /profil/password`
  (`ProfilController`). Versi API: `PUT /api/me` dan `PUT /api/me/password`.
- Demo: login role apa saja, buka Profil, ganti nama dan foto, simpan. Lalu
  ganti password, logout, login lagi dengan password baru.
- Jawaban singkat: "Profilnya satu halaman untuk semua role, datanya selalu
  milik user yang sedang login."

## 6. Form register khusus role peminjam

- Fitur baru: halaman Register dari halaman login ("Daftar sebagai
  peminjam"), route `GET/POST /register`. Role dipaksa `peminjam` di
  controller, tidak ada pilihan role di form, jadi tidak bisa dipakai membuat
  admin atau petugas. Setelah daftar, user langsung login dan masuk katalog.
  Register API memang sudah begitu sejak awal (role dipaksa peminjam).
- Demo: logout, klik daftar, isi form, akun baru langsung masuk katalog
  sebagai peminjam. Cek di Kelola User admin: role-nya peminjam.
- Jawaban singkat: "Register ini khusus peminjam, role-nya dikunci di backend,
  bukan sekadar disembunyikan di form."

## 7. Jam di laporan dan cetak PDF/Excel

- Masalah: tombol Cetak PDF kemarin hanya muncul kalau filter diisi, dan
  cetak Excel belum ada sama sekali.
- Perbaikan: tombol Cetak PDF dan Cetak Excel sekarang selalu tampil di
  laporan admin dan petugas, keduanya mengikuti filter yang sedang aktif.
  PDF memakai Dompdf (sudah ada di composer.json), Excel diunduh sebagai file
  .xls. Isi laporan (layar, PDF, Excel) sekarang menampilkan jam pinjam,
  rencana kembali, dan jam kembali. Filter rentang tanggal juga disesuaikan:
  tanggal akhir dihitung sampai 23:59:59 agar data di hari itu tidak hilang.
- Demo: buka Laporan, klik Cetak PDF (file PDF terunduh), klik Cetak Excel
  (file .xls terunduh), buka keduanya: ada kolom jam.
- Jawaban singkat: "PDF-nya kemarin tersembunyi di balik filter dan Excel-nya
  belum dibuat, sekarang keduanya selalu tersedia dari filter yang sama."
