# Arsitektur — Sistem Dosen (Fase 1, Prioritas 4)

Blueprint Prioritas 4: *"Akun Dosen → Dashboard → Jadwal → Kelas →
Presensi → Materi → Tugas → UTS → UAS → Nilai → Dosen Wali → Bimbingan"*.

Beda mendasar dari Prioritas 1-3: ini **bukan** modul Filament admin baru.
Semua data (Kelas, Presensi, Materi, Tugas, Nilai) sudah ada strukturnya
sejak SIAKAD (Prioritas 2) — yang dibangun di sini adalah **portal
self-service** supaya dosen bisa akses & kelola data itu sendiri tanpa
lewat `/admin`, plus dua entitas yang genuinely baru: **Dosen Wali** dan
**Bimbingan**.

## Data baru

| Tabel/kolom | Model | Peran |
|---|---|---|
| `students.advisor_id` (kolom baru, FK ke `lecturers`) | `Student::advisor()` | Dosen Wali aktif mahasiswa — satu per mahasiswa, tanpa riwayat penggantian terpisah (beda dari `StudentStatusHistory`) karena blueprint tidak memintanya |
| `academic_advisings` | `App\Models\Dosen\AcademicAdvising` | Bimbingan Akademik — catatan konsultasi Dosen Wali dengan mahasiswa bimbingannya |

Dosen Wali ditugaskan lewat `StudentResource` (Filament, field baru "Dosen
Wali") oleh admin — bukan self-assign oleh dosen sendiri, konsisten
dengan bagaimana penugasan mengajar (`class_lecturers`) juga ditugaskan
admin, bukan dosen memilih kelasnya sendiri.

## Portal (`routes/dosen.php`, Livewire murni — bukan Filament)

Digate middleware `lecturer` (`App\Http\Middleware\EnsureUserIsLecturer`,
alias `lecturer` di `bootstrap/app.php`) — beda dari `admin`/`permission:`
yang menggerbangi `/admin`: cukup cek `$user->hasRole('dosen')` **dan**
`$user->lecturer` ada (akun bisa saja diberi role dosen tapi data
`Lecturer`-nya belum lengkap). `App\Models\User::lecturer()` (relasi baru)
jadi jembatan dari akun login ke data Dosen.

| Route | Livewire Component | Isi |
|---|---|---|
| `GET /dosen` | `Dashboard` | Ringkasan: jumlah kelas diampu, jumlah mahasiswa bimbingan, jadwal hari ini |
| `GET /dosen/jadwal` | `Jadwal` | Jadwal mengajar mingguan, dikelompokkan per hari |
| `GET /dosen/kelas` | `Kelas\Index` | Daftar kelas yang diampu (lewat `Lecturer::classes()`, relasi baru — `BelongsToMany` ke `CourseClass` lewat `class_lecturers`, arah kebalikan dari `CourseClass::lecturers()` yang sudah ada) |
| `GET /dosen/kelas/{class}` | `Kelas\Show` | **Halaman inti** — satu halaman, empat tab (Presensi, Materi, Tugas, Nilai), meniru pola "relation-manager-sebagai-tab" yang sudah dipakai di seluruh Filament resource sesi ini, tapi murni Livewire |
| `GET /dosen/wali` | `DosenWali\Advisees` | Daftar mahasiswa bimbingan (`Lecturer::advisees()`, relasi baru) |
| `GET /dosen/wali/{student}` | `DosenWali\Bimbingan` | Catat & lihat riwayat bimbingan akademik untuk satu mahasiswa |

Layout: `#[Layout('components.layouts.app')]` — dosen memakai layout
autentikasi yang sama dengan Core System (nav bar + notification bell),
bukan bikin layout baru. Semua kelas Tailwind di view baru sengaja hanya
memakai kombinasi yang sudah dipakai di `livewire/pmb/register.blade.php`
dan `core/dashboard.blade.php`, supaya tidak perlu `npm run build` ulang
— dikonfirmasi lewat network request bersih (semua asset `200 OK`, tidak
ada kelas yang hilang dari compiled CSS) saat verifikasi browser.

### `Kelas\Show` — empat tab dalam satu komponen

- **Presensi** — dosen buat `ClassSession` (pertemuan), lalu tandai
  `Attendance` (hadir/izin/sakit/alpa) untuk tiap mahasiswa di roster.
  Roster diambil dari `KrsDetail::where('class_id', ...)` (mahasiswa yang
  benar-benar KRS kelas ini), bukan dari tabel terpisah — Single Source
  of Truth dengan domain KRS yang sudah ada.
- **Materi** — unggah `TeachingMaterial` per pertemuan, pakai trait
  Livewire bawaan `WithFileUploads` (bukan paket tambahan), disk & folder
  sama dengan yang dipakai `MaterialsRelationManager` Filament
  (`local`/`teaching-materials`) — file yang diunggah lewat portal dan
  lewat admin panel akhirnya mendarat di tempat yang sama.
- **Tugas** — buat `Assignment`, pilih tugas untuk lihat/nilai
  `AssignmentSubmission` per mahasiswa (skor + umpan balik).
- **Nilai** — buat `GradeComponent`, input `Grade` per mahasiswa per
  komponen, lalu **Finalisasi** per mahasiswa — memanggil
  `GradeFinalization::computeWeightedScore()` +
  `GradeFinalization::scoreToScale()` yang **sama persis** dengan yang
  dipakai tombol "Hitung Ulang" di `GradeFinalizationResource` Filament
  (domain Nilai) — satu logika kalkulasi, dua titik akses (admin & dosen
  sendiri), tidak digandakan.

Semua empat tab memanggil model Eloquent yang sama yang sudah dipakai
Filament resource dari SIAKAD (Prioritas 2) — portal ini tidak
menciptakan skema/logika bisnis baru untuk kelas, hanya membuka jalur
akses baru ke skema yang sudah ada.

## Otorisasi dua lapis

1. **Middleware `lecturer`** — akun harus punya role `dosen` + baris
   `Lecturer`.
2. **Per-halaman, di `mount()`** — `Kelas\Show` mengecek
   `$class->lecturers()->where('lecturer_id', $lecturer->id)->exists()`;
   `DosenWali\Bimbingan` mengecek `$student->advisor_id ===
   $lecturer->id`. Dosen A tidak bisa mengelola kelas dosen B atau
   membimbing mahasiswa dosen B lain hanya dengan mengganti ID di URL.

## Verifikasi

Diverifikasi **hanya lewat klik UI browser sungguhan** (login sebagai
akun dosen asli, bukan superadmin) — konsisten dengan cara seluruh sesi
ini memverifikasi UI, tapi domain ini murni antarmuka pengguna jadi tidak
ada langkah tinker terpisah untuk "logika inti", cukup data seed awal
lewat tinker (dosen + kelas + jadwal + mahasiswa + Dosen Wali + KRS)
lalu semua alur dites dengan login sungguhan sebagai dosen tersebut:

- Login sebagai `dosen.test@az-zahra.ac.id` → Dashboard menampilkan
  jumlah kelas/bimbingan yang benar + jadwal hari ini.
- Kelas Saya → Kelola → tab **Presensi**: buat pertemuan, tandai hadir,
  simpan → flash "Presensi tersimpan."
- Tab **Nilai**: tambah komponen "UTS" bobot 100% → input skor 88 →
  **Finalisasi** → nilai akhir tampil `88.00` (cocok: 100% × 88 = 88).
- Tab **Tugas**: buat tugas → flash "Tugas dibuat."
- Tab **Materi**: unggah materi (tanpa file, jalur `nullable`) → flash
  "Materi ditambahkan."
- Dosen Wali → Bimbingan: catat sesi konsultasi → langsung muncul di
  Riwayat Bimbingan.
- **Batas otorisasi**: mengakses `/dosen/kelas/{id}` untuk kelas yang
  BUKAN diampu dosen ini (dibuat khusus untuk tes) → `403 Anda bukan
  pengampu kelas ini.` — dikonfirmasi HTTP status & pesan, bukan cuma
  UI yang menyembunyikan tombol.

## Yang masih terbuka

- **Belum ada portal Mahasiswa** — blueprint modul "11. Sistem Mahasiswa"
  (self-service registrasi, KRS, jadwal, nilai, tagihan) belum
  dibangun; struktur `routes/dosen.php` + middleware `lecturer` di sini
  bisa jadi cetak biru untuk `routes/mahasiswa.php` + middleware serupa
  nanti.
- **UTS/UAS sebagai entitas terjadwal** (`App\Models\Ujian\Exam`) belum
  ada tab tersendiri di portal — dosen tetap input nilai UTS/UAS lewat
  tab **Nilai** sebagai `GradeComponent` biasa (sudah cukup untuk
  perhitungan nilai akhir), tapi jadwal/peserta ujian formal
  (`ExamResource`) masih admin-only.
- **Materi belum ada halaman unduh untuk mahasiswa** — file tersimpan di
  disk `local`, belum ada portal mahasiswa untuk mahasiswa mengaksesnya
  balik (terkait gap di atas).
- Gap Dosen Wali yang sudah dicatat di `docs/krs.md` ("Approver belum
  divalidasi harus Dosen Wali mahasiswa bersangkutan") **sekarang bisa
  diperbaiki** — `Student.advisor_id` sudah ada, jadi
  `ApprovalsRelationManager` di domain KRS bisa memfilter opsi approver
  ke Dosen Wali mahasiswa terkait di iterasi berikutnya.
