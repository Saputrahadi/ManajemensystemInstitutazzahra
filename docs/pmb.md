# Arsitektur — PMB (Fase 1, Prioritas 1)

Domain PMB (Penerimaan Mahasiswa Baru) — bagian pertama Fase 1 sesuai
blueprint `#prioritas`: *"Website → PMB → Nomor Pendaftaran → Akun Calon
Mahasiswa → Tagihan PMB → BSI → Pembayaran → Verifikasi → Seleksi → Lulus →
Registrasi Ulang → NIM"*.

## Status saat ini

**Skema data** — 12 migrasi + 12 model persis sesuai tabel domain PMB di
"Database MVP (Tahap 1)" blueprint `#erd`.

**Pendaftaran publik** — sudah bisa dipakai end-to-end:
- `GET /pmb/daftar` ([routes/pmb.php](../routes/pmb.php)) → Livewire
  `App\Livewire\Pmb\Register` ([app/Livewire/Pmb/Register.php](../app/Livewire/Pmb/Register.php)),
  view [resources/views/livewire/pmb/register.blade.php](../resources/views/livewire/pmb/register.blade.php).
  Tidak butuh login, mengikuti pola `App\Livewire\Auth\Login`.
- Nomor pendaftaran dijamin unik per periode lewat
  `App\Services\Pmb\ApplicantRegistrationService` ([app/Services/Pmb/ApplicantRegistrationService.php](../app/Services/Pmb/ApplicantRegistrationService.php)) —
  row-lock (`lockForUpdate`) di `admission_periods.last_sequence`, pola yang
  sama dengan `SkCounter` di project sistem-yayasan-laravel. Format:
  `{KODE-PERIODE}-{urut 4 digit}`, mis. `PMB-2027-2028-GEL1-0001`.

**Admin (Filament, navigation group "PMB")**:
- `AdmissionPeriodResource` — CRUD periode.
- `AdmissionPathResource` — CRUD jalur pendaftaran.
- `AdmissionTestResource` — jadwal ujian seleksi per periode.
- `ApplicantResource` — lihat & kelola pendaftar (tanpa halaman create — Applicant hanya dibuat lewat form publik, bukan diinput admin secara manual). Digate oleh `AdmissionPeriodPolicy`/`AdmissionPathPolicy`/`AdmissionTestPolicy`/`ApplicantPolicy` (berbeda dari `UnitResource`/`RoleResource`/`PermissionResource` di Core yang masih belum ada Policy — untuk PMB, semuanya sengaja permission-gated sejak awal).

**Alur seleksi penuh, terpasang di `ApplicantResource`**:
- **Verifikasi berkas** — relation manager "Dokumen" (`DocumentsRelationManager`): upload + aksi cepat Terverifikasi/Tolak per dokumen. Aksi tabel "Verifikasi Administrasi" mencatat `ApplicantVerification` lewat `ApplicantVerificationService` dan mengubah `Applicant.status`.
- **Hasil ujian** — relation manager "Hasil Ujian" (`TestResultsRelationManager`): pilih `AdmissionTest`, input nilai.
- **Wawancara** — relation manager "Wawancara" (`InterviewsRelationManager`): jadwalkan, lalu aksi baris "Isi Hasil" menyimpan `InterviewResult` (1:1) lewat modal.
- **Rekap skor** — aksi tabel "Hitung Skor" memanggil `AdmissionScoringService::recompute()` — rata-rata seluruh `admission_test_results` + `interview_results` milik Applicant, upsert ke `AdmissionScore`. Tidak otomatis terpicu — dijalankan manual per Applicant setelah semua nilai masuk.
- **Keputusan** — aksi tabel "Tetapkan Keputusan" (diterima/ditolak/cadangan) lewat `AdmissionDecisionService`, mengubah `Applicant.status` ke `lulus`/`tidak_lulus`.
- **Registrasi ulang → NIM** — aksi tabel "Registrasi Ulang" (hanya tampil jika `status === 'lulus'`) memanggil `ReRegistrationService::register()`, menerbitkan NIM lewat `NimCounter` (row-locked per Prodi+tahun ajaran, format `{2 digit tahun}{study_program_id 2 digit}{urut 4 digit}` — persis meniru contoh `27010001` di blueprint), lalu set `Applicant.status = 'selesai'`.

Semua service ada di `App\Services\Pmb\*` — dipanggil dari `Action::action()` Filament lewat dependency injection (`function (Applicant $record, XxxService $service)`), diverifikasi jalan lewat tinker **dan** klik UI asli.

## Tabel & model (`App\Models\Pmb\*`)

| Tabel | Model | Peran |
|---|---|---|
| `admission_periods` | `AdmissionPeriod` | Periode/gelombang PMB |
| `admission_paths` | `AdmissionPath` | Jalur pendaftaran (Reguler, Beasiswa, RPL, ...) — data-driven seperti `Unit::type` |
| `applicants` | `Applicant` | Entitas inti — Calon Mahasiswa |
| `applicant_documents` | `ApplicantDocument` | Dokumen upload + status verifikasi per-dokumen |
| `applicant_verifications` | `ApplicantVerification` | Riwayat keputusan verifikasi administrasi (level pendaftaran, bukan per-dokumen) |
| `admission_tests` | `AdmissionTest` | Jadwal ujian seleksi |
| `admission_test_results` | `AdmissionTestResult` | Hasil ujian per Calon Mahasiswa |
| `interviews` | `Interview` | Jadwal wawancara |
| `interview_results` | `InterviewResult` | Hasil wawancara (1:1 ke `Interview`) |
| `admission_scores` | `AdmissionScore` | Rekap skor gabungan per Calon Mahasiswa (1:1) — dasar keputusan |
| `admission_decisions` | `AdmissionDecision` | Keputusan akhir (diterima/ditolak/cadangan), 1:1 |
| `re_registrations` | `ReRegistration` | Registrasi ulang — `nim` terisi di sini |
| `nim_counters` | `NimCounter` | **Tambahan di luar 12 tabel ERD blueprint** — counter NIM per Prodi+tahun ajaran (row-locked), pola sama dengan `admission_periods.last_sequence`. Tidak pakai `Auditable` (murni bookkeeping angka, sama seperti `SkCounter` di project sistem-yayasan-laravel). |

Semua model (kecuali `NimCounter`) pakai trait `Auditable` ([app/Models/Core/Concerns/Auditable.php](../app/Models/Core/Concerns/Auditable.php)) — perubahan data PMB otomatis masuk Audit Log, sama seperti domain Core.

## Kenapa `applicant_documents` terpisah dari `documents` (Core)

Sengaja tidak numpang ke tabel `documents` generik di Core System — dokumen
PMB butuh field workflow verifikasi per-berkas (`status`, `verified_by`,
`verified_at`) yang tidak relevan untuk dokumen Core (SK, surat resmi,
dst.). Ini juga persis mengikuti daftar tabel `#erd` blueprint, yang
memang memisahkan keduanya.

## `re_registrations.nim` — belum ada tabel `students`

Blueprint menegaskan NIM dan status aktif adalah dua hal berbeda (lihat
`#intimahasiswa`): NIM terbit begitu registrasi ulang selesai, status aktif
baru menyusul setelah UKT lunas. Kolom `nim` sengaja ditaruh di
`re_registrations`, **bukan** foreign key ke tabel `students` — domain
Mahasiswa (dengan `students`/`student_profiles`/dst.) belum dibangun.
Begitu domain itu ada, tambahkan `student_id` lewat migrasi baru; jangan
ubah migrasi `re_registrations` yang sudah ada.

## Permission

Empat kode baru lewat [PmbPermissionSeeder](../database/seeders/PmbPermissionSeeder.php)
(pola modul terpisah — lihat docblock `PermissionSeeder` Core):
`pmb.period.manage`, `pmb.applicant.view`, `pmb.applicant.manage`,
`pmb.decision.manage` — dipakai oleh Policy PMB (lihat di atas).
`super_admin` otomatis dapat semuanya lewat `'*'` di `RoleSeeder`, role
lain belum diberi akses eksplisit. Role "Operator PMB" dari blueprint
`#akses` juga belum dibuat (lihat catatan gap yang sama di `RoleSeeder` —
masih pakai role generik).

## RBAC/data-scoping — belum diterapkan ke `Applicant`

`HasUnitScope` tidak ditempel ke `Applicant` karena scoping-nya secara
alami lewat `study_program_id`, bukan `unit_id` langsung — trait yang ada
belum mendukung scoping lewat kolom tidak langsung/join. Ini keputusan
terbuka untuk dipecahkan begitu ada kebutuhan nyata (mis. Admin PMB Prodi A
tidak boleh lihat pendaftar Prodi B).
