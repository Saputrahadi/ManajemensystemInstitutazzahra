# Arsitektur — Perkuliahan (Fase 1, Prioritas 2 — SIAKAD)

## Tabel & model (`App\Models\Perkuliahan\*`)

| Tabel | Model | Peran |
|---|---|---|
| `class_sessions` | `ClassSession` | Satu pertemuan kuliah dari sebuah Kelas (`App\Models\Kelas\CourseClass`) — nomor pertemuan unik per kelas |
| `attendances` | `Attendance` | Presensi mahasiswa per pertemuan — satu baris per mahasiswa per `class_session` |
| `teaching_materials` | `TeachingMaterial` | Materi ajar yang diunggah untuk satu pertemuan tertentu |
| `assignments` | `Assignment` | Tugas untuk satu Kelas — tidak diikat ke pertemuan tertentu karena tenggat bisa lintas pertemuan |
| `assignment_submissions` | `AssignmentSubmission` | Pengumpulan tugas — satu baris per mahasiswa per `assignment` |

**Model bernama `ClassSession`, bukan `Session`** — `Session` berpotensi
ambigu dengan sesi HTTP Laravel meskipun secara teknis tidak bentrok
namespace; dipilih nama yang eksplisit mengikuti pola `CourseClass` di
domain Kelas & Jadwal (menghindari nama generik untuk entitas domain).

## Relasi kunci

- `CourseClass::sessions()` / `CourseClass::assignments()` — `HasMany`,
  ditambahkan ke model `CourseClass` yang sudah ada di domain Kelas &
  Jadwal (FK eksplisit `class_id`, konsisten dengan `schedules()` yang
  sudah lebih dulu ada di sana).
- `ClassSession::attendances()` / `::materials()` — `HasMany`.
- `Assignment::submissions()` — `HasMany`.
- `Attendance::student()` / `AssignmentSubmission::student()` — `BelongsTo`
  ke `App\Models\Mahasiswa\Student`.

## Admin (Filament, navigation group "Perkuliahan")

- `ClassSessionResource` — dua relation manager: **Presensi**
  (`AttendancesRelationManager`, status hadir/izin/sakit/alpa) dan
  **Materi** (`MaterialsRelationManager`, `FileUpload` ke disk `local`
  direktori `teaching-materials`, pola sama dengan
  `DocumentsRelationManager` di `ApplicantResource`).
- `AssignmentResource` — satu relation manager **Pengumpulan**
  (`SubmissionsRelationManager`, `FileUpload` direktori
  `assignment-submissions`, field `score`/`feedback` diisi manual oleh
  admin/dosen setelah menilai).

Digate lewat Policy (`ClassSessionPolicy`, `AssignmentPolicy`) dan
permission dari `PerkuliahanPermissionSeeder`:
`perkuliahan.session.{view,manage}`, `perkuliahan.assignment.{view,manage}`
— `Attendance`/`TeachingMaterial`/`AssignmentSubmission` tidak punya Policy
sendiri, ikut permission resource induknya (`ClassSession`/`Assignment`)
lewat relation manager, pola yang sama dipakai domain Kurikulum & Kelas
& Jadwal untuk anak-anak pivot/`HasMany`-nya.

## Verifikasi

Tinker: create `ClassSession` dari `CourseClass` → `Attendance` (status
`hadir`) → `TeachingMaterial` → `Assignment` dari `CourseClass` yang sama →
`AssignmentSubmission` dengan skor — semua relasi baca-balik benar
(`$session->courseClass->code`, `$attendance->student->nim`, dst). UI
Filament asli: `ClassSessionResource` edit page menampilkan tab
**Presensi** dan **Materi** dengan data yang sama persis dari tinker;
`AssignmentResource` edit page menampilkan tab **Pengumpulan** dengan NIM,
nama, waktu kumpul, dan skor yang benar.

## Yang masih terbuka

- **Belum ada portal self-service** — sama seperti domain KRS, presensi,
  pengumpulan tugas, dan unggah materi masih lewat Filament admin, bukan
  lewat alur yang diisi dosen/mahasiswa sendiri.
- **`Assignment.max_score` belum divalidasi terhadap `AssignmentSubmission.score`**
  — admin bisa memasukkan skor lebih besar dari `max_score` tanpa
  penolakan.
- **Presensi belum otomatis diisi dari jadwal (`schedules`)** — tiap
  `class_session` harus dibuat manual, belum ada generator pertemuan
  otomatis dari `CourseOffering` + jumlah pertemuan per semester.
- Belum ada agregasi rekap presensi per mahasiswa per kelas (persentase
  kehadiran) — baru relevan setelah domain Nilai/Hasil Akademik dibangun,
  karena presensi minimum biasanya jadi syarat ikut UAS.
