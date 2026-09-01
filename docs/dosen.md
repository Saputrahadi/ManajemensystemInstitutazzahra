# Arsitektur — Dosen (Fase 1, Prioritas 2 — SIAKAD)

## Tabel & model (`App\Models\Dosen\*`)

| Tabel | Model | Peran |
|---|---|---|
| `lecturers` | `Lecturer` | Entitas inti — NIDN/NIDK, jabatan akademik, status kepegawaian |
| `lecturer_profiles` | `LecturerProfile` | Biodata (1:1) |
| `lecturer_educations` | `LecturerEducation` | Riwayat pendidikan (1:banyak) |
| `lecturer_expertises` | `LecturerExpertise` | Bidang keahlian (1:banyak) |
| `lecturer_assignments` | `LecturerAssignment` | Penugasan ke Prodi (1:banyak — satu dosen bisa punya lebih dari satu jenis penugasan sekaligus, mis. `dosen` + `dosen_wali` di Prodi yang sama, sesuai contoh rantai akses di blueprint #unit) |

## Beda dengan Mahasiswa: dibuat langsung, bukan dikonversi

Tidak ada alur pendaftaran seperti PMB → Applicant → Student. Dosen dibuat
langsung oleh admin lewat `LecturerResource`, mengikuti pola
`EmployeeController` di project sistem-yayasan-laravel: satu form membuat
`User` (akun login, role `dosen`) + `Lecturer` sekaligus dalam satu
transaksi (`Pages\CreateLecturer::handleRecordCreation()`). Edit juga
begitu (`Pages\EditLecturer::handleRecordUpdate()`), termasuk field
`name`/`email` yang sebenarnya milik `User` tapi ditampilkan di form yang
sama (`mutateFormDataBeforeFill` menyuntikkannya saat form dibuka).

Diverifikasi lewat klik UI Filament asli: submit form → redirect ke Edit →
`User` + `Lecturer` + role `dosen` semuanya benar (dicek tinker).

## Admin (Filament, navigation group "Dosen")

`LecturerResource` dengan relation manager Riwayat Pendidikan, Keahlian,
dan Penugasan. Digate `LecturerPolicy`
(`dosen.lecturer.view`/`.manage`, seeder `DosenPermissionSeeder`).

## Yang masih terbuka

- Belum ada penugasan otomatis "Dosen Pengampu" ke mata kuliah/kelas —
  domain Kurikulum & Kelas belum dibangun, jadi `lecturer_assignments`
  baru bisa dikaitkan ke Prodi, belum ke mata kuliah spesifik.
- Role `dosen` masih generik — blueprint #akses sudah merevisi jadi "Dosen
  Pengampu" vs "Dosen Wali" terpisah (catatan gap yang sama seperti di
  `RoleSeeder` untuk domain PMB).
