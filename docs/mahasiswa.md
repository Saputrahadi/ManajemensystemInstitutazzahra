# Arsitektur — Mahasiswa (Fase 1, Prioritas 2 — SIAKAD)

Domain Mahasiswa — dibangun duluan di antara sub-domain SIAKAD karena
`ReRegistration.nim` (hasil PMB) sebelumnya menganggur tanpa tabel
`students` untuk menampungnya (lihat catatan "utang" di `docs/pmb.md`
sebelum ini). Lihat blueprint #intimahasiswa: *"Applicant → Student → User
— dikonversi otomatis, tidak diinput ulang"*.

## Tabel & model (`App\Models\Mahasiswa\*`)

| Tabel | Model | Peran |
|---|---|---|
| `students` | `Student` | Entitas inti — field persis sesuai blueprint #erd ("Entitas Core dengan field") |
| `student_profiles` | `StudentProfile` | Biodata (1:1) |
| `student_addresses` | `StudentAddress` | Alamat (1:banyak — KTP vs domisili) |
| `student_families` | `StudentFamily` | Data keluarga/wali (1:banyak) |
| `student_documents` | `StudentDocument` | Dokumen resmi (ijazah, dst.) |
| `student_status_histories` | `StudentStatusHistory` | Riwayat status — append-only, tidak pakai `Auditable` (riwayatnya sendiri sudah jadi audit trail) |

`students.curriculum_id` sengaja **tanpa foreign key** — domain Kurikulum
belum ada. Tambahkan constraint-nya lewat migrasi baru begitu `curricula`
dibangun, jangan ubah migrasi `create_students_table`.

## Konversi Applicant → Student

`App\Services\Mahasiswa\StudentEnrollmentService::enroll(ReRegistration $reRegistration)`
— dipanggil lewat aksi "Buat Akun Mahasiswa" di `ApplicantResource`
(tampil hanya kalau `Applicant.status === 'selesai'` dan `ReRegistration`
belum terhubung ke `Student`). Dalam satu transaksi:

1. Buat `User` (username = NIM, role `mahasiswa`, password acak).
2. Buat `Student` dengan `student_status = 'registrasi'` — **bukan** langsung
   `aktif`, karena blueprint menegaskan status Aktif baru menyusul setelah
   UKT lunas (domain Keuangan belum dibangun di sini).
3. Salin biodata/alamat/wali dari `Applicant` ke `StudentProfile`/
   `StudentAddress`/`StudentFamily` (Single Source of Truth — tidak
   diinput ulang).
4. Catat `StudentStatusHistory` awal.
5. Hubungkan balik `ReRegistration.student_id`.

Diverifikasi lewat tinker (skenario penuh: daftar → keputusan → NIM →
konversi) **dan** klik UI Filament asli (tombol → modal konfirmasi →
record muncul di `StudentResource`).

## Admin (Filament, navigation group "Mahasiswa")

`StudentResource` — lihat & kelola Mahasiswa, dengan relation manager
Alamat, Keluarga/Wali, dan Riwayat Status (append-only — menambah baris
baru otomatis memperbarui `Student.student_status`, tidak ada Edit/Delete
di situ). Digate `StudentPolicy` (`mahasiswa.student.view`/`.manage`,
seeder terpisah `MahasiswaPermissionSeeder`).

Belum ada relation manager untuk Dokumen (`StudentDocument`) — menyusul
kalau dibutuhkan, polanya sama dengan `DocumentsRelationManager` di PMB.

## Yang masih terbuka

- `student_status` masih manual diubah admin — belum ada pemicu otomatis
  dari pembayaran UKT (domain Keuangan belum ada) untuk transisi
  `registrasi` → `aktif`.
- Belum ada `HasUnitScope` di `Student` — sama seperti `Applicant`,
  scoping alaminya lewat `study_program_id`, bukan `unit_id` langsung.
