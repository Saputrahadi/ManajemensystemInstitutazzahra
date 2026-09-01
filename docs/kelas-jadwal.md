# Arsitektur — Kelas & Jadwal (Fase 1, Prioritas 2 — SIAKAD)

## Tabel & model (`App\Models\Kelas\*`)

| Tabel | Model | Peran |
|---|---|---|
| `course_offerings` | `CourseOffering` | Pembukaan satu Mata Kuliah pada satu tahun akademik + `term` (ganjil/genap) |
| `classes` | `CourseClass` | Kelas/rombel paralel dari satu `CourseOffering` (mis. IF101-A, IF101-B) |
| `class_lecturers` | (pivot) | Dosen pengampu satu kelas, dengan `role` (`pengampu`/`asisten`) — bisa lebih dari satu dosen per kelas |
| `rooms` | `Room` | Master ruang kelas — lintas Prodi, tidak terikat `study_program_id` |
| `schedules` | `Schedule` | Slot jadwal satu kelas (hari, jam mulai/selesai, ruang) — satu kelas bisa punya lebih dari satu slot (mis. teori + praktikum) |

**Model bernama `CourseClass`, bukan `Class`** — `class` adalah kata kunci
cadangan PHP dan tidak bisa dipakai sebagai nama class. Tabelnya tetap
`classes` sesuai blueprint; model diarahkan lewat `protected $table = 'classes'`.

## Relasi kunci

- `CourseOffering::course()` — `BelongsTo` ke `App\Models\Kurikulum\Course`.
- `CourseOffering::classes()` — `HasMany` ke `CourseClass`.
- `CourseClass::courseOffering()` — `BelongsTo`.
- `CourseClass::lecturers()` — `BelongsToMany` ke `App\Models\Dosen\Lecturer`
  lewat `class_lecturers`, **FK pivot custom** (`'class_id', 'lecturer_id'`)
  karena kolom pivotnya `class_id`, bukan default Eloquent `course_class_id`
  yang seharusnya diturunkan dari nama model `CourseClass` — jangan hapus
  argumen FK eksplisit ini kalau model di-rename.
- `CourseClass::schedules()` — `HasMany` ke `Schedule` (FK eksplisit `class_id`,
  masalah penamaan yang sama dengan di atas).
- `Schedule::room()` — `BelongsTo` ke `Room`, nullable (`room_id` nullable —
  kelas daring/tanpa ruang fisik tetap bisa punya jadwal).

## Admin (Filament, navigation group "Kelas & Jadwal")

- `CourseOfferingResource` — relation manager **Kelas** (CRUD kelas paralel
  langsung dari halaman pembukaan mata kuliah).
- `ClassResource` — resource utama untuk kelas, dua relation manager:
  **Dosen Pengampu** (attach/detach `Lecturer` lewat `AttachAction` dengan
  form pivot `role`, pola sama dengan `CoursesRelationManager` di domain
  Kurikulum) dan **Jadwal** (CRUD `Schedule`, hari ditampilkan sebagai label
  Indonesia lewat `formatStateUsing`, bukan angka `1`-`7` mentah).
- `RoomResource` — CRUD sederhana master ruang.

Semua digate lewat Policy (`CourseOfferingPolicy`, `CourseClassPolicy`,
`RoomPolicy`) dan permission dari `KelasPermissionSeeder`:
`kelas.offering.{view,manage}`, `kelas.class.{view,manage}`,
`kelas.room.{view,manage}`.

## Bug yang ditemukan & diperbaiki saat verifikasi

`CourseClass::lecturers()` awalnya memakai `belongsToMany(Lecturer::class,
'class_lecturers')` tanpa FK eksplisit — Eloquent menurunkan FK pivot dari
nama model (`CourseClass` → `course_class_id`), tapi migrasi
`class_lecturers` memakai kolom `class_id`. Ini baru ketahuan lewat tinker
(`QueryException: table class_lecturers has no column named
course_class_id`), bukan dari review kode. Diperbaiki dengan FK eksplisit
`belongsToMany(Lecturer::class, 'class_lecturers', 'class_id',
'lecturer_id')`. `schedules()` sudah benar sejak awal karena FK-nya memang
ditulis eksplisit.

## Verifikasi

Tinker: create `CourseOffering` → `CourseClass` → attach `Lecturer` (pivot
`role`) → create `Room` + `Schedule`, semua relasi & pivot terbaca balik
dengan benar setelah fix di atas. UI Filament asli: `ClassResource` edit
page menampilkan tab **Dosen Pengampu** (NIDN/NIDK, nama, peran) dan
**Jadwal** (hari berlabel "Senin", jam, ruang) dengan benar;
`CourseOfferingResource` edit page menampilkan tab **Kelas**; `RoomResource`
list menampilkan data ruang.

## Yang masih terbuka

- Belum ada validasi bentrok jadwal (dosen/ruang dobel booking di
  hari+jam yang sama) — perlu domain ini stabil dulu sebelum ditambahkan.
- `CourseOffering.quota` belum dihitung otomatis dari jumlah mahasiswa
  ambil kelas — baru relevan setelah domain KRS dibangun.
- `lecturer_assignments` (domain Dosen, terikat ke Prodi) dan
  `class_lecturers` (domain ini, terikat ke kelas spesifik) masih dua
  mekanisme terpisah yang belum disinkronkan.
