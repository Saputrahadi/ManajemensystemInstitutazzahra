# SOP — Setup Development Lokal

## Prasyarat

- PHP 8.3+ dengan ekstensi: `intl`, `zip`, `pdo_sqlite` atau `pdo_pgsql`
  (lihat catatan versi PHP di bawah)
- Composer
- Node.js + npm
- PostgreSQL (opsional untuk lokal — lihat alternatif SQLite di bawah)

## Langkah setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# DB default di .env.example adalah PostgreSQL
createdb az_zahra_digital_campus

php artisan migrate --seed
npm install
npm run build          # atau `npm run dev` saat development
php artisan filament:assets
php artisan serve
```

Login pengguna biasa: `http://localhost:8000/login`
Panel admin (Filament): `http://localhost:8000/admin`

Akun Super Admin dari seeder: `superadmin@az-zahra.ac.id`, password dari
`SEED_SUPERADMIN_PASSWORD` di `.env` (isi ini **sebelum** menjalankan
`migrate --seed` — kalau kosong, fallback-nya `ganti-password-ini`, jangan
dipakai di production).

## Alternatif tanpa PostgreSQL/Redis (lokal saja)

`.env.example` default ke `pgsql`/`redis` untuk DB/session/cache/queue.
Untuk dev lokal tanpa service tambahan, ini kombinasi yang terbukti jalan:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Buat file database-nya dulu: `touch database/database.sqlite` (Windows:
buat file kosong lewat cara apa pun).

## Masalah yang sudah pernah ditemui

Lihat bagian **Known setup gotchas** di [CLAUDE.md](../../CLAUDE.md) untuk
daftar lengkap — ringkasnya:

1. **`php artisan filament:assets` wajib dijalankan** setelah `composer
   install`/`composer update` yang menyentuh paket `filament/*`. Kalau
   lupa, sidebar panel admin dan widget upload file akan error JavaScript
   secara diam-diam (halaman tetap tampil, tapi interaksinya rusak).
2. **`composer.json` pakai `optimize-autoloader: true`** — kalau
   menambah file class PHP baru (Resource, Policy, dll.), jalankan
   `composer dump-autoload` supaya Laravel bisa menemukannya. Tanpa ini
   akan muncul error `Class "..." not found` yang membingungkan karena
   sekilas tidak berhubungan dengan file yang baru ditambah.
3. **PHP 8.3+ benar-benar dibutuhkan** untuk menjalankan test suite
   (`php artisan test`) — dependency `sebastian/environment` (dipakai
   PHPUnit 12) memakai syntax typed class constant yang cuma ada di PHP
   8.3 ke atas. Menjalankan APLIKASI-nya sendiri masih bisa di PHP 8.2
   dengan `composer install --ignore-platform-req=php`, tapi test suite
   tidak akan bisa jalan sampai PHP di-upgrade.
