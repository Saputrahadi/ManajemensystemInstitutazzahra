# Arsitektur — Kurikulum (Fase 1, Prioritas 2 — SIAKAD)

## Tabel & model (`App\Models\Kurikulum\*`)

| Tabel | Model | Peran |
|---|---|---|
| `curricula` | `Curriculum` | Kurikulum per Prodi/tahun akademik (mis. "Kurikulum 2026") |
| `courses` | `Course` | Mata kuliah, milik satu Prodi |
| `curriculum_courses` | (pivot) | Mata kuliah mana masuk kurikulum mana, dengan `semester` + `is_mandatory` |
| `course_prerequisites` | (pivot, self-referencing) | Mata kuliah prasyarat (`course_id` ← `prerequisite_course_id`, keduanya ke `courses`) |
| `learning_outcomes` | `LearningOutcome` | CPL (Capaian Pembelajaran Lulusan), milik satu Prodi |
| `course_learning_outcomes` | `CourseLearningOutcome` | CPMK — pemetaan satu Mata Kuliah ke satu CPL, dengan kode/deskripsi CPMK sendiri |

`students.curriculum_id` (kolom sudah ada sejak domain Mahasiswa dibangun,
tapi tanpa FK karena `curricula` belum ada) sekarang mendapat FK sungguhan
lewat migrasi `2026_08_21_500160_add_curriculum_foreign_to_students_table`
— pola yang sama dipakai sebelumnya untuk `re_registrations.nim`: kolom
lebih dulu, constraint menyusul saat domain targetnya sudah dibangun. Jangan
ubah migrasi `create_students_table` yang lama.

## Relasi kunci

- `Curriculum::courses()` / `Course::curricula()` — `BelongsToMany` lewat
  `curriculum_courses`, `withPivot(['semester', 'is_mandatory'])`.
- `Course::prerequisites()` — `BelongsToMany` self-referencing ke `Course`
  lewat `course_prerequisites` (`course_id`, `prerequisite_course_id`).
- `Course::learningOutcomes()` — `HasMany` ke `CourseLearningOutcome` (CPMK
  milik mata kuliah ini).
- `CourseLearningOutcome::learningOutcome()` — `BelongsTo` ke CPL induk.
- `Student::curriculum()` — `BelongsTo` ke `Curriculum` (baru ditambahkan).

## Admin (Filament, navigation group "Kurikulum")

- `CurriculumResource` — relation manager **Mata Kuliah dalam Kurikulum**
  (attach mata kuliah + isi `semester`/`is_mandatory` lewat form pivot di
  `AttachAction`).
- `CourseResource` — dua relation manager: **Mata Kuliah Prasyarat**
  (attach/detach ke `Course` lain, record dirinya sendiri dikecualikan dari
  pilihan) dan **CPMK** (create/edit/delete `CourseLearningOutcome`, pilihan
  CPL difilter ke Prodi yang sama dengan mata kuliahnya).
- `LearningOutcomeResource` — CRUD sederhana untuk master CPL per Prodi.

Semua digate lewat Policy (`CurriculumPolicy`, `CoursePolicy`,
`LearningOutcomePolicy`) dan permission dari `KurikulumPermissionSeeder`:
`kurikulum.curriculum.{view,manage}`, `kurikulum.course.{view,manage}`,
`kurikulum.outcome.{view,manage}`.

## Verifikasi

Dicek lewat tinker (create Curriculum + 2 Course, attach dengan pivot
semester, self-referencing prerequisite, CPL→CPMK) dan lewat klik UI
Filament asli: list & edit page `CurriculumResource` menampilkan pivot
`semester` dengan benar, tab **Mata Kuliah Prasyarat** di `CourseResource`
menampilkan mata kuliah prasyarat yang di-attach lewat tinker, tab **CPMK**
menampilkan mapping CPL yang benar.

## Yang masih terbuka

- Belum ada validasi "prasyarat harus sudah lulus" — itu baru relevan
  setelah domain Nilai/Hasil Akademik dibangun.
- `Curriculum.status` (`draft`/`aktif`/`nonaktif`) belum menggerakkan
  apa pun secara otomatis (mis. mahasiswa baru otomatis pakai kurikulum
  `aktif`) — masih murni field informatif, akan dipakai saat alur KRS
  dibangun.
- `lecturer_assignments` (domain Dosen) masih terikat ke Prodi saja, belum
  ke `Course` spesifik — sekarang `Course` sudah ada, jadi ini bisa
  disambungkan di iterasi Kelas & Jadwal berikutnya.
