# Arsitektur — Ujian / UTS-UAS (Fase 1, Prioritas 2 — SIAKAD)

## Tabel & model (`App\Models\Ujian\*`)

| Tabel | Model | Peran |
|---|---|---|
| `exams` | `Exam` | UTS/UAS untuk satu Kelas — `type` (`uts`/`uas`), satu kelas hanya boleh punya satu baris per jenis (`unique(class_id, type)`) |
| `exam_participants` | `ExamParticipant` | Mahasiswa terdaftar/hadir pada satu ujian |
| `exam_submissions` | `ExamSubmission` | Jawaban ujian yang dikumpulkan mahasiswa beserta skornya |

`exam_participants` dan `exam_submissions` sengaja dipisah, bukan
digabung: kehadiran fisik (peserta datang ke ruang ujian) dan pengumpulan
jawaban (file/nilai) adalah dua hal yang bisa berbeda — mahasiswa bisa
hadir tapi tidak mengumpulkan (dianggap tidak menjawab), atau (untuk
ujian daring) mengumpulkan tanpa baris kehadiran fisik.

## Relasi kunci

- `CourseClass::exams()` — `HasMany`, ditambahkan ke model `CourseClass`
  yang sudah ada (FK eksplisit `class_id`, konsisten dengan
  `sessions()`/`assignments()` dari domain Perkuliahan).
- `Exam::participants()` / `Exam::submissions()` — `HasMany`.
- `ExamParticipant::student()` / `ExamSubmission::student()` — `BelongsTo`
  ke `App\Models\Mahasiswa\Student`.

## Admin (Filament, navigation group "Ujian")

`ExamResource` (label plural "Ujian (UTS/UAS)") dengan dua relation
manager: **Peserta** (`ParticipantsRelationManager` — status
terdaftar/hadir/tidak_hadir) dan **Pengumpulan Jawaban**
(`SubmissionsRelationManager` — `FileUpload` direktori
`exam-submissions`, field `score`/`feedback`), pola form/table identik
dengan `AssignmentResource` di domain Perkuliahan.

Digate `ExamPolicy` dan permission dari `UjianPermissionSeeder`:
`ujian.exam.{view,manage}` — `ExamParticipant`/`ExamSubmission` tidak
punya Policy sendiri, ikut permission `Exam` induknya lewat relation
manager, pola yang sama dipakai domain Perkuliahan untuk
`Attendance`/`TeachingMaterial`/`AssignmentSubmission`.

## Verifikasi

Tinker: create `Exam` (UTS) dari `CourseClass` → `ExamParticipant` (status
`hadir`) → `ExamSubmission` (skor 90) → create `Exam` kedua (UAS, sukses,
jenis berbeda) → percobaan create UTS kedua untuk kelas yang sama
**ditolak** oleh `unique(class_id, type)` (`QueryException`, dicek lewat
try/catch) — mengonfirmasi constraint "satu kelas cuma satu UTS/satu
UAS" benar-benar ditegakkan di level database, bukan cuma asumsi desain.
UI Filament asli: list `ExamResource` menampilkan UTS dan UAS sebagai dua
baris terpisah untuk kelas yang sama; edit page menampilkan tab
**Peserta** dan **Pengumpulan Jawaban** dengan data yang sama persis
dengan hasil tinker.

## Yang masih terbuka

- **Peserta belum otomatis diisi dari KRS** — idealnya semua mahasiswa
  yang mengambil kelas ini di `krs_details` otomatis jadi baris
  `exam_participants` berstatus `terdaftar`; sekarang masih diisi manual
  satu-satu lewat relation manager, sama seperti gap presensi di
  `docs/perkuliahan.md`.
- **Skor ujian dan skor tugas belum digabung jadi nilai akhir** — itu
  ranah domain Nilai (Prioritas 2 berikutnya), yang biasanya punya bobot
  UTS/UAS/Tugas per mata kuliah.
- Belum ada validasi jadwal ujian tidak bentrok dengan `schedules` kelas
  lain di ruang/waktu yang sama — gap yang sama dengan yang dicatat di
  `docs/kelas-jadwal.md`.
