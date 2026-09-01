# Az-Zahra Digital Campus — Fase 0 (Core System)

Kode sumber Fase 0 sesuai Master Blueprint Institut Az-Zahra: **Login → User → Role → Permission → Unit/Organisasi → Audit Log → Notifikasi → Dokumen → Settings**.

## ⚠️ Penting sebelum menjalankan

Proyek ini ditulis di sandbox cloud yang **tidak punya akses ke Packagist** (registry Composer/PHP) — hanya npm/PyPI/crates.io yang diizinkan di sana. Jadi:

- Semua **kode aplikasi** (migrasi, model, policy, middleware, Livewire, Filament resource, seeder, test) sudah lengkap dan sudah dicek sintaksnya (`php -l`, tanpa error).
- Folder **`vendor/`** (Laravel framework + Livewire + Filament + dependensi lain) **belum ada** — harus di-generate dengan `composer install` di komputer/server Anda yang punya akses internet normal.
- Karena `vendor/` tidak ada, kode di sini **belum pernah benar-benar dijalankan/dites** di sandbox ini. Jalankan `composer install` lalu `php artisan test` di lingkungan Anda sebagai langkah pertama, sebelum lanjut membangun Fase 1.

## Setup (di komputer/server dengan internet)

```bash
composer install
cp .env.example .env
php artisan key:generate

# Sesuaikan .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD (PostgreSQL sudah dibuat lebih dulu)
createdb az_zahra_digital_campus   # atau lewat tool PostgreSQL pilihan Anda

php artisan migrate
php artisan db:seed
# atau sekaligus: php artisan migrate --seed

npm install
npm run build   # atau `npm run dev` saat development

php artisan serve
```

Setelah `db:seed`, akun Super Admin awal:

- **Email:** `superadmin@az-zahra.ac.id`
- **Password:** nilai `SEED_SUPERADMIN_PASSWORD` di `.env` (isi ini SEBELUM seeding; default fallback `ganti-password-ini` — jangan dipakai di production).

Login pengguna biasa: `http://localhost:8000/login`
Panel admin (Filament, CRUD Core System): `http://localhost:8000/admin`

Jalankan test:

```bash
php artisan test
```

## Apa yang sudah dibangun (Fase 0)

| Bagian | Lokasi | Catatan |
|---|---|---|
| Skema database | `database/migrations/` | Urut sesuai dependensi: users → organizational_units → institutions/faculties/study_programs → roles/permissions → pivot → audit_logs → notifications → documents → settings. Nama tabel & pivot mengikuti ERD blueprint persis (`organizational_units`, `user_roles`, `role_permissions`) |
| Model + relasi | `app/Models/Core/` (+ `App\Models\User` di root, sengaja tidak dipindah) | `User`, `Unit` (tree self-referencing, tabel `organizational_units`), `Institution`/`Faculty`/`StudyProgram` (companion table 1:1 ke `Unit`), `Role`, `Permission`, `AuditLog`, `Document`, `Setting` |
| RBAC + data-scoping | `app/Models/Core/Concerns/HasUnitScope.php`, `app/Policies/` | `User::visibleTo($user)` — scoping otomatis berdasar unit yang ditugaskan ke role, termasuk unit turunan |
| Middleware permission | `app/Http/Middleware/EnsureUserHasPermission.php` | Dipasang lewat alias `permission:<code>` di route |
| Audit Trail otomatis | `app/Models/Core/Concerns/Auditable.php` | Trait — tempel ke model manapun, mencatat created/updated/deleted tanpa perlu kode manual di Controller |
| Login | `app/Livewire/Auth/Login.php` + `resources/views/livewire/auth/login.blade.php` | Rate limiting, cek status akun, catat ke Audit Log |
| Panel Admin (Filament) | `app/Filament/Resources/` | CRUD User, Role, Permission, Unit, Document, Setting; Audit Log viewer (read-only). `Institution`/`Faculty`/`StudyProgram` belum punya Resource |
| Seed data awal | `database/seeders/` | 18 kategori unit (+ 4 Prodi awal: HKI/MBS/PAI/PBA, sekaligus companion row `institutions`/`faculties`/`study_programs`), 12 role inti, permission Core, 1 Super Admin |
| API v1 | `routes/api.php` → `routes/core-api.php`, `app/Http/Controllers/Core/Api/` | `/api/v1/me`, `/api/v1/units`, `/api/v1/audit-logs` — standar response sesuai blueprint #api |
| Test RBAC & Audit | `tests/Feature/Core/RbacUnitScopingTest.php`, `tests/Feature/Core/AuditLogTest.php` | Skenario resmi: "Kaprodi Prodi A tidak bisa lihat data Prodi B" |

## Definition of Done Fase 0 (dari blueprint) — status kode

- [x] Login & RBAC berjalan dengan data-scoping — **kode ada**, tunggu `php artisan test` lolos di lingkungan Anda untuk verifikasi.
- [x] Setiap perubahan data tercatat di Audit Log — trait `Auditable`, teruji lewat `AuditLogTest`.
- [x] Struktur Unit/Organisasi bisa dikonfigurasi tanpa ubah kode — tabel `units` tree + panel Filament `UnitResource`.
- [ ] Server staging berjalan dengan skema database penuh — **langkah Anda**: jalankan `composer install` + `migrate` di server staging sesuai `.env.example`.

Begitu empat butir di atas hijau semua di lingkungan Anda, lanjut ke **Fase 1 (PMB + Identity + Payment)** sesuai Urutan Pembangunan di Master Blueprint.

## Struktur folder

Mengikuti konvensi di Master Blueprint bagian "Struktur Folder Aplikasi": namespace yang sama dipakai konsisten di `Controllers/`, `Models/`, `Services/`, `resources/views/`, dan `routes/` untuk tiap modul. Modul `Core` ada di folder-folder di atas; modul berikutnya (PMB, Akademik, Keuangan, dst.) mengikuti pola yang sama begitu dibangun.

## Belum termasuk di Fase 0 (sengaja)

Sesuai keputusan "jangan mulai dari PMB sebelum fondasi selesai": NIM, Mahasiswa, PMB, Kurikulum, KRS, Nilai, Tagihan/UKT, integrasi BSI/PDDIKTI/EMIS — semua itu masuk Fase 1 ke atas. `.env.example` sudah menyiapkan slot kredensial BSI/PDDIKTI/EMIS supaya tidak perlu restrukturisasi `.env` nanti, tapi implementasinya menyusul.
