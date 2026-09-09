# Master Blueprint — YPHR Smart System (Az-Zahra Digital Campus)

Dokumen ini adalah **rangkuman blueprint dari apa yang sudah benar-benar
dibangun** di repository ini, disusun ulang jadi satu referensi utuh —
bukan pengganti [CLAUDE.md](../CLAUDE.md) (yang tetap jadi sumber detail
paling lengkap untuk AI agent), tapi versi yang lebih mudah dibaca sebagai
peta sistem: apa yang sudah selesai, bagaimana potongannya saling
berhubungan, dan apa yang belum dikerjakan.

Nama proyek kode: **Az-Zahra Digital Campus** — sistem digital kampus
(SIAKAD) untuk Institut Az-Zahra, dikembangkan di bawah payung **YPHR
Smart System**. Ditulis di atas Laravel 11, sebagai aplikasi standalone
(bukan package).

## 1. Ringkasan Status

| Fase | Cakupan | Status |
|---|---|---|
| Fase 0 | Core System (Login, User, Role, Permission, Unit/Organisasi, Audit Log, Notifikasi, Dokumen, Settings) | ✅ Selesai |
| Fase 1, Prioritas 1 | PMB (Penerimaan Mahasiswa Baru) | ✅ Selesai end-to-end |
| Fase 1, Prioritas 2 | SIAKAD — Mahasiswa, Dosen, Kurikulum, Kelas & Jadwal, KRS, Perkuliahan, Ujian (UTS/UAS), Nilai, Hasil Akademik | ✅ Selesai (9 sub-domain) |
| Fase 1, Prioritas 3 | Keuangan & Pembayaran + Integrasi Bank BSI | ✅ Selesai |
| Fase 1, Prioritas 4 | Sistem Dosen (portal self-service dosen) | ✅ Selesai |
| Fase berikutnya | PDDIKTI/EMIS, Administrasi Internal (SDM/Aset/Persuratan/Perpustakaan/dll.), portal self-service Mahasiswa | ⬜ Belum dikerjakan |

Semua modul di atas sudah **dijalankan end-to-end secara nyata** — bukan
cuma lolos `php artisan test`, tapi diverifikasi lewat `tinker` dan klik
UI Filament/Livewire sungguhan (termasuk uji negatif, mis. dosen yang
membuka kelas orang lain mendapat `403` betulan). Rincian per domain ada
di dokumen masing-masing, lihat [docs/README.md](README.md).

## 2. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend framework | Laravel `^11.0`, PHP `^8.3` |
| Auth API | Laravel Sanctum `^4.0` |
| UI interaktif non-admin | Livewire `^3.5` |
| Panel admin/CRUD | Filament `^3.2` |
| File/media | Spatie Media Library `^11.9` |
| Frontend build | Vite + Tailwind CSS `^4.0` |
| Database | PostgreSQL (default `.env.example`); SQLite dipakai untuk test (`phpunit.xml`) dan dev lokal tanpa Postgres |
| Cache/session/queue | Redis (default `.env.example`); `file`/`sync` untuk dev lokal |
| CI | GitHub Actions, matrix PHP 8.3/8.4/8.5, `.github/workflows/tests.yml` |

## 3. Arsitektur Aplikasi

### 3.1 Konvensi namespace

Per modul, kode tersebar di lima lokasi paralel sesuai konvensi blueprint
`#folder`:

```
App\Models\<Modul>\*
App\Http\Controllers\<Modul>\*   (kalau ada Controller klasik)
App\Services\<Modul>\*
resources/views/<modul>/
routes/<modul>.php (+ <modul>-api.php kalau ada API)
```

Pengecualian yang disengaja:
- `App\Models\User` **tidak** dinamespace ke `Core\` — dia anchor
  Authenticatable yang dipakai langsung oleh `config/auth.php`, Sanctum,
  dan Filament.
- `app/Policies/`, `app/Livewire/`, `app/Filament/Resources/`, dan
  `app/Notifications/` **tidak** dipecah per-modul — auto-discovery
  Livewire/Filament membuat pemecahan itu rapuh tanpa manfaat nyata.

### 3.2 Layering

`Presentation → Application (Controller/Livewire) → Domain (Service) →
Data (Eloquent Model) → Infrastructure`. Logic bisnis yang lebih dari
sekadar orkestrasi request/response hidup di `App\Services\<Modul>\*`
(mis. `PaymentVerificationService`, `StudentEnrollmentService`), bukan di
Controller. Beberapa domain sederhana (KRS, Ujian, Perkuliahan) sengaja
tidak punya Service class — logicnya cukup lewat model event.

### 3.3 Dua permukaan aplikasi

1. **Filament admin panel** di `/admin` — CRUD untuk modul yang sifatnya
   pure-CRUD administratif (PMB, Mahasiswa, Dosen, Kurikulum, Kelas,
   KRS, Perkuliahan, Ujian, Nilai, Hasil Akademik, Keuangan, dan Core
   System). Setiap modul dapat nav group sendiri.
2. **Livewire self-service portal** untuk peran non-admin — saat ini baru
   **Sistem Dosen** (`/dosen`, `routes/dosen.php`), belum ada padanan
   untuk Mahasiswa. Login umum (non-Filament) ada di `/login`
   (`App\Livewire\Auth\Login`).

Ini keputusan arsitektur eksplisit dari blueprint: **tidak semua modul
otomatis dapat Filament resource** — modul yang butuh alur kerja
role-spesifik (self-service dosen) dibangun sebagai Livewire di atas data
yang sama, bukan diplester ke Filament.

### 3.4 RBAC & Data-Scoping (inti sistem)

- `User` ↔ `Role` many-to-many lewat `user_roles`; pivot punya `unit_id` —
  `null` berarti role institution-wide, terisi berarti role di-scope ke
  satu Unit.
- `Role` ↔ `Permission` many-to-many lewat `role_permissions`.
- `Unit` (tabel `organizational_units`) adalah tree self-referencing,
  `type` bebas teks — jenis unit baru ditambah lewat data, bukan migrasi.
- Trait `HasUnitScope` → `Model::query()->visibleTo($user)`: scoping
  otomatis ke unit yang ditugaskan ke user, **termasuk semua unit
  turunannya**.
- Middleware `permission:<code>` (route-level, default-deny) + Policy
  (row-level, gabungan cek permission + `sameOrDescendantUnit()`).
- Skenario uji kanonis: **"Kaprodi Prodi A tidak bisa lihat data Prodi
  B"** — [tests/Feature/Core/RbacUnitScopingTest.php](../tests/Feature/Core/RbacUnitScopingTest.php).

Detail lengkap: [docs/architecture.md](architecture.md).

### 3.5 Audit Trail otomatis

Trait `Auditable` menempel ke event `created`/`updated`/`deleted` model
apa pun dan otomatis mencatat before/after diff + pelaku + IP + user
agent ke `AuditLog`, tanpa kode manual di Controller/Service. `AuditLog`
bersifat append-only — Policy-nya menolak update/delete untuk siapa pun,
termasuk Super Admin.

### 3.6 API

`routes/api.php` cuma thin loader; tiap domain punya file
`routes/<domain>-api.php` sendiri (`core-api.php`, `keuangan-api.php`,
dst.), versi `/v1`, Sanctum-authenticated, response envelope tetap:
`{success, data, meta}` / `{success, message, errors}`. Pengecualian:
`POST /api/bsi/callback` — webhook masuk dari bank BSI, autentikasi pakai
token statis (`X-Callback-Token`), bukan Sanctum.

## 4. Peta Modul yang Sudah Dibangun

| Modul | Namespace Model | Jumlah Model | Nav Group Filament | Dokumen |
|---|---|---|---|---|
| Core System | `App\Models\Core\*` (+ `User`) | 10 | Core System | [architecture.md](architecture.md) |
| PMB | `App\Models\Pmb\*` | 13 | PMB | [pmb.md](pmb.md) |
| Mahasiswa | `App\Models\Mahasiswa\*` | 6 | Mahasiswa | [mahasiswa.md](mahasiswa.md) |
| Dosen | `App\Models\Dosen\*` | 6 | Dosen | [dosen.md](dosen.md) |
| Kurikulum | `App\Models\Kurikulum\*` | 4 | Kurikulum | [kurikulum.md](kurikulum.md) |
| Kelas & Jadwal | `App\Models\Kelas\*` | 4 | Kelas & Jadwal | [kelas-jadwal.md](kelas-jadwal.md) |
| KRS | `App\Models\Krs\*` | 3 | KRS | [krs.md](krs.md) |
| Perkuliahan | `App\Models\Perkuliahan\*` | 5 | Perkuliahan | [perkuliahan.md](perkuliahan.md) |
| Ujian (UTS/UAS) | `App\Models\Ujian\*` | 3 | Ujian | [ujian.md](ujian.md) |
| Nilai | `App\Models\Nilai\*` | 5 | Nilai | [nilai.md](nilai.md) |
| Hasil Akademik | `App\Models\HasilAkademik\*` | 6 | Hasil Akademik | [hasil-akademik.md](hasil-akademik.md) |
| Keuangan + BSI | `App\Models\Keuangan\*` | 11 | Keuangan | [keuangan.md](keuangan.md) |
| Sistem Dosen (portal) | *(pakai model domain di atas)* | — | *(bukan Filament — Livewire `/dosen`)* | [sistem-dosen.md](sistem-dosen.md) |

Total: **76 model domain** di 12 namespace + `User`, **88 migrasi**, 25+
resource Filament (lihat `app/Filament/Resources/`).

### 4.1 Alur inti lintas modul ("Alur Inti Mahasiswa")

Ini jalur end-to-end yang menyatukan hampir semua modul di atas, dan
sudah diverifikasi berjalan sungguhan dari ujung ke ujung:

```
Pendaftar publik (/pmb/daftar, tanpa login)
  → Seleksi PMB (verifikasi dokumen, tes, wawancara, keputusan)
  → Registrasi Ulang → NIM diterbitkan (NimCounter, row-locked)
  → StudentEnrollmentService: Applicant → Student + User (role mahasiswa)
  → KRS diambil (snapshot SKS dari Course, sinkron ke Krs.total_credits)
  → Perkuliahan berjalan (presensi, materi, tugas)
  → UTS/UAS (Exam, ExamParticipant, ExamSubmission)
  → Nilai dihitung otomatis dari GradeComponent → GradeFinalization
  → Hasil Akademik: IP semester & IPK kumulatif dihitung otomatis
     dari seluruh GradeFinalization
  → Tagihan UKT diterbitkan (Invoice) → VA BSI → Payment
  → PaymentVerificationService: begitu invoice kategori "ukt" lunas,
     Student.student_status berubah registrasi → aktif
  → Transcript / GraduationRecord di akhir studi
     (GraduationRecord juga otomatis mengubah student_status → lulus)
```

Dosen berinteraksi dengan sebagian besar tahap ini lewat portal
self-service `/dosen` (Sistem Dosen), bukan lewat Filament.

## 5. Konvensi Penting

- **Nama tabel database mengikuti ERD blueprint persis**, bukan default
  Laravel yang idiomatis: `organizational_units` (bukan `units`),
  `user_roles` (bukan `role_user`), `role_permissions` (bukan
  `permission_role`), dst. — pola yang sama dipertahankan di semua modul
  sesudahnya.
- **Row-locked sequence counter** untuk nomor yang harus unik & berurutan
  (NIM, nomor dokumen pembayaran) — pola `NimCounter` /
  `PaymentDocumentCounter`, dipakai konsisten di PMB dan Keuangan.
- **"Human Decision, System Execution"** — keputusan yang butuh
  wewenang manusia (approval perpanjangan jatuh tempo, approval nilai,
  approval KRS) dieksekusi lewat satu model event (`booted()`/`created`)
  begitu baris approval dibuat: perubahan status, notifikasi, dan efek
  turunannya terjadi dalam satu transaksi, bukan dipisah ke Controller.
- **Relation-manager-as-tab** — baik di Filament (relation manager) maupun
  di Livewire (`Kelas\Show` dengan 4 tab), child records dari satu entity
  induk ditampilkan sebagai tab, bukan resource terpisah, kalau child itu
  tidak masuk akal berdiri sendiri (Presensi, Materi, Pengumpulan Jawaban,
  dll.).
- **Kode, string UI, dan referensi blueprint dalam Bahasa Indonesia**;
  bagian blueprint dirujuk inline sebagai `#<section>` (mis. `#keamanan`,
  `#arsitektur`, `#api`).
- Style: preset `laravel` via StyleCI, `laravel/pint` tersedia sebagai
  dev dependency.

## 6. Known Gotchas (ringkasan — lihat CLAUDE.md untuk detail penuh)

Ditemukan lewat menjalankan aplikasi sungguhan, bukan cuma baca kode:

1. `laravel/pao` (dev) tidak kompatibel dengan Laravel `^11.0` — sudah
   dihapus dari `require-dev`.
2. `config/database.php` sempat pakai `Pdo\Mysql::ATTR_SSL_CA` (class
   native PHP 8.4-only) yang dievaluasi eager — crash di PHP < 8.4 — sudah
   diganti ke `\PDO::MYSQL_ATTR_SSL_CA`.
3. Migrasi `documents` sempat dobel index untuk `documentable` — sudah
   dihapus yang redundan.
4. `composer.json` pin `optimize-autoloader: true` (classmap-based) —
   class PHP baru butuh `composer dump-autoload` supaya kepakai.
5. `php artisan filament:assets` wajib dijalankan ulang setelah
   `composer update` yang menyentuh paket `filament/*`, kalau tidak
   chrome panel admin rusak diam-diam.
6. SQLite mengembalikan kolom `decimal`-cast sebagai float mentah setelah
   model di-query ulang, memicu deprecation `brick/math` — solusinya
   `getRawOriginal()` di titik baca aritmatika (lihat
   `StudentSemesterResult::computeForPeriod()`). Postgres/MySQL tidak
   kena masalah ini (mengembalikan string).

## 7. Yang Belum Dikerjakan

- **Integrasi PDDIKTI/EMIS** — slot konfigurasi sudah disiapkan di
  `.env.example` supaya tidak perlu restrukturisasi `.env` nanti, tapi
  implementasinya belum ada.
- **Administrasi Internal** — SDM, Aset & Sarpras, Persuratan,
  Perpustakaan, dan modul administratif internal lain dari daftar 26
  domain blueprint belum disentuh sama sekali.
- **Portal self-service Mahasiswa** — belum ada padanan dari `/dosen`
  untuk mahasiswa; KRS saat ini murni dikelola lewat Filament admin.
- **Automasi jadwal nyata** — dua Artisan command Keuangan
  (`keuangan:mark-overdue`, `keuangan:send-reminders`) sudah ada tapi
  belum dikaitkan ke cron/scheduler OS sungguhan.
- **Kelengkapan permission** — `UnitResource`/`RoleResource`/
  `PermissionResource` di Filament belum punya Policy class, jadi siapa
  pun yang login ke `/admin` saat ini bisa mengelolanya tanpa
  permission-gating. Perlu ditutup sebelum staging/production.
- 12 role inti sudah di-seed (dari 29 tipe user penuh di blueprint) —
  turunan role spesifik (Admin PMB, Admin Keuangan, dll.) dimaksudkan
  ditambah lewat `RoleResource` di Filament, bukan seeder baru.

## 8. Referensi

- [CLAUDE.md](../CLAUDE.md) — panduan lengkap untuk AI agent, sumber detail
  paling mutakhir untuk tiap modul (diperbarui setiap sesi kerja).
- [docs/README.md](README.md) — indeks semua dokumen domain.
- [docs/architecture.md](architecture.md) — detail RBAC, audit trail,
  struktur Fase 0.
- [docs/sop/setup-local.md](sop/setup-local.md) dan
  [docs/sop/deployment.md](sop/deployment.md) — SOP setup & deploy.
