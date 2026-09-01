# Arsitektur — Core System (Fase 0)

Fase 0 mencakup sembilan bagian sesuai Master Blueprint (`#core`): **Login →
User → Role → Permission → Unit/Organisasi → Audit Log → Notification →
File/Dokumen → Settings**. Dokumen ini menjelaskan bagaimana bagian-bagian
itu saling berhubungan di kode.

## Posisi Fase 0 di skema database 26 domain

Struktur database Master Blueprint dikelompokkan per domain, satu level di
atas skema tabel per domain. Fase 0 menyentuh 6 dari 26 domain:

| Domain (blueprint) | Tabel di Fase 0 |
|---|---|
| Organisasi | `organizational_units`, `institutions`, `faculties`, `study_programs` |
| User & Access | `users`, `roles`, `permissions`, `role_permissions`, `user_roles` |
| Audit | `audit_logs` |
| Notifikasi | `notifications` |
| File & Document | `documents` |
| Core | `settings` |

19 domain lainnya (PMB, Mahasiswa, Dosen, Akademik, Kurikulum, RPL,
Keuangan, BSI, Kemahasiswaan, SDM, Aset & Sarpras, Persuratan,
Perpustakaan, Penelitian, Pengabdian, Mutu, SPI, Alumni, Integration)
belum ada — semuanya Fase 1 ke atas.

**Status penyelarasan dengan ERD blueprint** (sudah dikerjakan menyusul
temuan awal bahwa skemanya menyimpang):
- Tabel & nama pivot sudah **persis** sama dengan tabel "Database MVP
  (Tahap 1)" di blueprint: `organizational_units` (bukan `units`),
  `user_roles` (bukan `role_user`), `role_permissions` (bukan
  `permission_role`).
- `institutions`/`faculties`/`study_programs` sekarang tabel & model
  tersendiri (`App\Models\Core\Institution`/`Faculty`/`StudyProgram`),
  bukan cuma baris generik di `organizational_units` — field khususnya
  (dekan, akreditasi, jenjang, dst.) sekarang punya tempat. Desainnya
  companion table 1:1 ke `organizational_units` lewat
  `organizational_unit_id`, supaya RBAC/`HasUnitScope` di bawah **tidak
  perlu diubah sama sekali** — baris institut/fakultas/prodi tetap ada di
  tree `organizational_units` seperti sebelumnya, cuma sekarang punya
  "detail tambahan" di tabel pendamping. Lihat `Unit::institution()`/
  `faculty()`/`studyProgram()` di [app/Models/Core/Unit.php](../app/Models/Core/Unit.php).
- Namespace kode sudah dipindah ke `Core/` di lima tempat sesuai konvensi
  blueprint `#folder` — `app/Models/Core/`, `app/Http/Controllers/Core/`,
  `app/Services/Core/`, `resources/views/core/`, `routes/core.php` +
  `routes/core-api.php` — **kecuali** model `User` yang sengaja tetap di
  `App\Models\User` (anchor Authenticatable yang dipakai `config/auth.php`,
  Sanctum, dan Filament secara langsung; memindahkannya menambah risiko
  besar untuk manfaat kecil).

**Yang masih sengaja digabung**: keenam domain di atas masih satu Filament
navigation group ("Core System") dan satu folder `tests/Feature/Core/` —
bukan dipisah per nama domain granular seperti di tabel "Struktur
Database". Ini konsisten dengan blueprint sendiri: bagian `#core` (Fase 0)
membundel kesembilan blok ini sebagai satu kesatuan di level modul/fase,
terpisah dari daftar 26 domain yang levelnya lebih granular (level skema
database, bukan level pengorganisasian kode).

## RBAC & data-scoping per Unit

Ini adalah inti dari Fase 0 — semua bagian lain ada untuk mendukungnya.

- `User` ↔ `Role` adalah relasi many-to-many lewat tabel pivot `user_roles`,
  dan pivot itu sendiri punya kolom `unit_id`
  ([app/Models/User.php](../app/Models/User.php)). Kalau `unit_id` pada
  baris pivot bernilai `null`, berarti role itu **institution-wide**
  (`hasInstitutionWideRole()`). Kalau ada nilainya, role itu di-scope ke
  satu Unit tertentu.
- `Role` ↔ `Permission` juga many-to-many, lewat `role_permissions`.
  `User::hasPermission()` mengecek lintas semua role yang dimiliki user.
- `Unit` ([app/Models/Core/Unit.php](../app/Models/Core/Unit.php), tabel
  `organizational_units`) adalah tree self-referencing (`parent_id` /
  `children()` / `descendants()`). Kolom `type` bebas teks (institut,
  fakultas, prodi, biro, ukm, ...) — unit jenis baru ditambahkan lewat
  data, bukan lewat migrasi/kode.
- Trait `HasUnitScope`
  ([app/Models/Core/Concerns/HasUnitScope.php](../app/Models/Core/Concerns/HasUnitScope.php))
  menyediakan `Model::query()->visibleTo($user)`: role institution-wide
  melihat semua baris; selain itu query dibatasi ke gabungan
  `scopedUnitIds()` milik user **plus seluruh unit turunannya** (mis. Dekan
  yang di-scope ke Fakultas otomatis melihat semua Prodi di bawahnya).
- Middleware `permission:<code>`
  ([app/Http/Middleware/EnsureUserHasPermission.php](../app/Http/Middleware/EnsureUserHasPermission.php))
  mengunci route/group di level route — default-deny, butuh kode permission
  yang persis sama.
- Policy (mis.
  [app/Policies/UserPolicy.php](../app/Policies/UserPolicy.php)) mengunci
  di level baris data — kombinasi cek permission + `sameOrDescendantUnit()`.

**Skenario uji kanonis**: "Kaprodi Prodi A tidak bisa lihat data Prodi B" —
lihat
[tests/Feature/Core/RbacUnitScopingTest.php](../tests/Feature/Core/RbacUnitScopingTest.php).
Perubahan apa pun ke `HasUnitScope`/`scopedUnitIds()`/`hasInstitutionWideRole()`
wajib tetap membuat test ini lolos. Test ini tidak berubah sama sekali saat
`institutions`/`faculties`/`study_programs` dipisah — bukti bahwa desain
companion-table di atas berhasil menjaga RBAC tetap stabil.

## Audit Trail

Trait `Auditable`
([app/Models/Core/Concerns/Auditable.php](../app/Models/Core/Concerns/Auditable.php))
menempel ke event model `created`/`updated`/`deleted` dan otomatis menulis
baris ke `AuditLog` (before/after diff, pelaku, IP, user agent) — tempel
trait-nya ke model apa pun, tidak perlu kode manual di Controller. Kolom
sensitif/berisik bisa dikecualikan lewat properti
`protected array $auditExcept = [...]` pada model (lihat `User::$auditExcept`
yang mengecualikan `password`, `remember_token`, `last_login_at`,
`last_login_ip`).

`AuditLog` bersifat **append-only** — `AuditLogPolicy` selalu menolak
`update`/`delete`, termasuk untuk Super Admin. Jangan buat fitur atau script
yang menghapus/mengubah baris `audit_logs` (lihat
[scripts/maintenance.sh](../scripts/maintenance.sh) yang sengaja tidak
menyentuhnya).

## Dua jalur login

- `App\Livewire\Auth\Login`
  ([app/Livewire/Auth/Login.php](../app/Livewire/Auth/Login.php)) — login
  Core System untuk pengguna non-admin (Dosen/Mahasiswa di fase mendatang)
  di `/login`. Menerima email atau username, rate-limit 5x/60 detik, cek
  `status === 'aktif'`, dan mencatat `AuditLog` saat berhasil.
- Panel Filament di `/admin`
  ([app/Providers/Filament/AdminPanelProvider.php](../app/Providers/Filament/AdminPanelProvider.php))
  punya `->login()` sendiri, terpisah dari guard di atas. Di sinilah CRUD
  untuk User/Role/Permission/Unit/Dokumen/Pengaturan dan viewer Audit Log
  (read-only) berada.

## Dokumen, Pengaturan, dan Notifikasi

- **Dokumen** — `DocumentResource` (Filament) mengelola tabel `documents`
  yang polymorphic (`documentable_type`/`documentable_id`), dikunci lewat
  `DocumentPolicy` (permission tunggal `core.document.manage`). File
  disimpan di disk `local` (`storage/app/private`) secara default — belum
  publik kecuali kolom `is_public` diaktifkan.
- **Pengaturan** — `SettingResource` mengelola tabel `settings` (key/value,
  di-cache 1 jam lewat `Setting::get()`/`Setting::set()`). Karena Filament
  menyimpan langsung lewat Eloquent (bukan lewat `Setting::set()`),
  halaman create/edit-nya secara eksplisit membersihkan cache
  `setting:{key}` setelah simpan.
- **Notifikasi** — pakai channel `database` bawaan Laravel (`User` sudah
  pakai trait `Notifiable`). `App\Notifications\GenericNotification`
  adalah notifikasi generik judul/isi/link yang bisa dipanggil modul mana
  pun lewat `$user->notify(new GenericNotification(...))` tanpa perlu bikin
  class baru. Ditampilkan lewat komponen Livewire
  `<livewire:notification-bell />` di navbar.

Ketiganya ada di `App\Models\Core\{Document,Setting}` — Notifikasi tidak
punya model sendiri karena memakai tabel `notifications` bawaan Laravel
langsung.

## Lapisan Domain (Service)

`App\Services\Core\DashboardStatsService`
([app/Services/Core/DashboardStatsService.php](../app/Services/Core/DashboardStatsService.php))
adalah Service pertama di Core — memisahkan logic statistik dashboard
(jumlah user/unit ter-scope, dst.) dari `DashboardController`, sesuai
lapisan Domain di blueprint `#arsitektur` (Presentation → Application →
**Domain (Service/Action)** → Data → Infrastructure). Pola ini yang
diikuti begitu Controller lain butuh logic bisnis yang lebih dari
orkestrasi request/response sederhana.

## Permission yang sudah dikunci vs. belum

Hanya `User`, `AuditLog`, `Document`, dan `Setting` yang punya Policy class
(dan karena itu benar-benar dikunci lewat permission) di Filament. Resource
`Unit`, `Role`, dan `Permission` **belum** punya Policy — siapa pun yang
login ke panel admin saat ini bisa mengelolanya. `Institution`, `Faculty`,
`StudyProgram` juga belum punya Filament Resource sama sekali (baru
model+migrasi+seeder) — ini catatan penting kalau mau menutup gap
keamanan/kelengkapan CRUD sebelum staging/production.

## Urutan seeding

`Unit → Permission → Role → SuperAdmin`
([database/seeders/DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php))
— Unit harus ada dulu untuk scoping Role, Permission harus ada dulu untuk
`Role::permissions()->sync()`, dan Super Admin terakhir karena butuh
keduanya. `UnitSeeder` sendiri sekarang juga membuat baris
`institutions`/`faculties`/`study_programs` pendamping untuk tiap unit
institut/fakultas/prodi yang dibuatnya.
