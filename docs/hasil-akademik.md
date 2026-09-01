# Arsitektur — Hasil Akademik (Fase 1, Prioritas 2 — SIAKAD)

Domain terakhir dari Prioritas 2 (SIAKAD) — menutup rantai
Kurikulum → Kelas & Jadwal → KRS → Perkuliahan → Ujian → **Nilai** →
**Hasil Akademik**.

## Tabel & model (`App\Models\HasilAkademik\*`)

| Tabel | Model | Peran |
|---|---|---|
| `student_semester_results` | `StudentSemesterResult` | IP satu mahasiswa untuk satu tahun akademik + term |
| `student_cumulative_results` | `StudentCumulativeResult` | IPK kumulatif — satu baris per mahasiswa |
| `transcripts` | `Transcript` | Transkrip yang diterbitkan — snapshot beku, bukan link hidup |
| `graduation_requirements` | `GraduationRequirement` | Syarat kelulusan per Program Studi (`min_credits`, `min_gpa`) |
| `graduation_verifications` | `GraduationVerification` | Verifikasi pemenuhan syarat kelulusan seorang mahasiswa |
| `graduation_records` | `GraduationRecord` | Catatan kelulusan resmi |

## Rantai kalkulasi otomatis

Semua angka di domain ini dihitung dari `grade_finalizations` (domain
Nilai), bukan diinput manual:

1. **`StudentSemesterResult::computeForPeriod()`** — jumlah SKS
   `GradeFinalization` milik mahasiswa itu yang `CourseClass`-nya berada
   di tahun akademik + term tertentu, dikali `grade_point` masing-masing
   (angka mutu × SKS = "angka mutu total"), dibagi total SKS diambil →
   `semester_gpa`. `credits_earned` cuma menghitung SKS dengan
   `grade_point > 0` (tidak termasuk yang dapat E).
2. **`StudentCumulativeResult::recalculateForStudent()`** — dipanggil
   otomatis dari `StudentSemesterResult::booted()` (`saved`/`deleted`,
   pola sama dengan `KrsDetail` → `Krs::recalculateTotalCredits()`).
   **Sengaja menghitung ulang dari SEMUA `grade_finalizations`
   mahasiswa langsung**, bukan merata-ratakan `semester_gpa` per
   semester — supaya bobot SKS tiap mata kuliah individual tetap benar
   (rata-rata dari rata-rata akan salah kalau jumlah SKS tiap semester
   berbeda).
3. **`Transcript`** — `cumulative_gpa_snapshot`/`total_credits_snapshot`
   dibekukan dari `StudentCumulativeResult` saat baris dibuat (tidak
   ikut berubah kalau nilai mahasiswa dikoreksi setelahnya — transkrip
   yang sudah terbit tetap sebagaimana adanya).
4. **`GraduationVerification`** — `meets_requirements` disarankan
   otomatis (bandingkan `StudentCumulativeResult` vs `min_credits`/
   `min_gpa` di `GraduationRequirement`) kalau tidak diisi manual saat
   dibuat, tapi field ini muncul sebagai `Toggle` yang bisa ditimpa saat
   **edit** (disembunyikan saat create) untuk kasus pengecualian yang
   butuh penilaian verifikator.
5. **`GraduationRecord`** — `final_gpa` dibekukan dari
   `StudentCumulativeResult` kalau tidak diisi manual; `created` event
   langsung meng-update `Student.student_status` jadi `'lulus'`.

## Bug SQLite yang ditemukan & diperbaiki: `decimal` cast + `brick/math`

Laravel meng-implementasikan cast `decimal:N` lewat `Brick\Math\BigDecimal::of()`
([`HasAttributes::asDecimal()`](../vendor/laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasAttributes.php)).
**SQLite (lewat PDO) mengembalikan kolom NUMERIC/DECIMAL sebagai PHP
float asli**, bukan string seperti PostgreSQL/MySQL — begitu model
di-query ulang dari SQLite (bukan instance yang baru saja dibuat di
memori), membaca attribute ber-cast `decimal` lewat accessor biasa
(`$model->grade_point`) meneruskan float itu ke `BigNumber::of()`, yang
sudah men-deprecate menerima float mentah (akan jadi error di brick/math
0.15).

Ditemukan lewat `set_error_handler()` + `debug_print_backtrace()` di
tinker (bukan dari review kode statis) saat menghitung IP semester —
warning-nya muncul tepat di titik `GradeFinalization::where(...)->get()`
lalu dibaca ulang, bukan di titik model itu pertama kali dibuat. **Fix**:
di titik manapun kode aplikasi membaca attribute `decimal` dari model
yang di-query ulang untuk dipakai dalam aritmatika/perbandingan, pakai
`$model->getRawOriginal('kolom')` (byte mentah, bukan lewat cast
getter) lalu `(float)`/`(string)` manual — bukan `$model->kolom`
langsung. Diterapkan di `StudentSemesterResult::computeForPeriod()`,
`StudentCumulativeResult::recalculateForStudent()`,
`GraduationVerification::booted()`, `Transcript::booted()`,
`GraduationRecord::booted()`, dan `GradeChangeApproval::booted()` (domain
Nilai — bug yang sama, baru ketahuan efeknya di sini).

**Catatan**: kolom Filament (`TextColumn::make('final_score')`, dst.) di
berbagai Resource yang sudah ada (Krs, GradeFinalization, dll.) tetap
memanggil accessor biasa untuk menampilkan nilai — ini masih memicu
warning yang sama saat halaman admin dirender di SQLite, tapi **cuma
kosmetik** (nilai yang ditampilkan tetap benar, tidak masuk ke
perhitungan lebih lanjut) dan tidak diperbaiki di semua tempat karena
scope-nya sangat lebar dan efeknya nol terhadap kebenaran data — beda
dengan titik-titik di atas yang hasilnya benar-benar dipakai untuk
aritmatika lanjutan. Deviasi database dev (`sqlite` menggantikan `pgsql`
produksi) sudah dicatat sebagai gotcha di README/CLAUDE.md; PostgreSQL
mengembalikan `decimal` sebagai string, jadi masalah ini **tidak akan
muncul di produksi**.

## Admin (Filament, navigation group "Hasil Akademik")

Enam resource flat (tanpa relation manager — tidak ada hubungan
pivot/hierarki bertingkat seperti domain-domain sebelumnya):

- `StudentSemesterResultResource`, `TranscriptResource`,
  `GraduationRequirementResource`, `GraduationVerificationResource`,
  `GraduationRecordResource` — CRUD penuh.
- `StudentCumulativeResultResource` — **tanpa halaman Create**
  (`canCreate(): false`), karena barisnya murni hasil
  `recalculateForStudent()`, bukan data yang pernah diinput manual.

Digate lewat Policy (`StudentSemesterResultPolicy`,
`StudentCumulativeResultPolicy`, `TranscriptPolicy`,
`GraduationRequirementPolicy`, `GraduationVerificationPolicy`,
`GraduationRecordPolicy`) dan permission dari
`HasilAkademikPermissionSeeder`: `hasil_akademik.result.{view,manage}`
(dipakai bareng oleh dua resource IP/IPK), `hasil_akademik.transcript.manage`,
`hasil_akademik.graduation.manage` (dipakai bareng oleh syarat/verifikasi/
catatan kelulusan).

## Verifikasi

Tinker (2 mata kuliah semester ganjil, 3+2 SKS, nilai A + BC): `semester_gpa`
terhitung `3.40` (manual: (3×4.00+2×2.50)/5 = 17/5 = 3.40, cocok persis),
`cumulative_gpa` ikut `3.40` (baru satu semester). Ditambah kasus kedua
(semester genap, mata kuliah gagal/E) untuk memverifikasi
`credits_earned` benar mengecualikan SKS yang gagal sementara
`cumulative_gpa` tetap memakai total SKS diambil sebagai pembagi (bukan
cuma yang lulus) — hasil `2.13` cocok dengan perhitungan manual
(17+0)/8. `Transcript`/`GraduationVerification`/`GraduationRecord` semua
mengambil snapshot yang benar dari `StudentCumulativeResult` saat itu.
UI Filament asli: keenam resource dicek lewat browser — IP Semester,
IPK Kumulatif (tanpa tombol "New"), Transkrip, Syarat Kelulusan,
Verifikasi Kelulusan (ikon centang hijau utk `meets_requirements=true`),
Catatan Kelulusan — semua menampilkan angka yang identik dengan hasil
tinker, dan `Student.student_status` berubah jadi `lulus` secara
otomatis setelah `GraduationRecord` dibuat (dicek di halaman
`StudentResource`).

## Yang masih terbuka — sekaligus penutup Fase 1 Prioritas 2 (SIAKAD)

Dengan domain ini selesai, **seluruh 8 sub-domain SIAKAD milik
Prioritas 2** (Mahasiswa, Dosen, Kurikulum, Kelas & Jadwal, KRS,
Perkuliahan, Ujian, Nilai, Hasil Akademik — 9 nama tapi Nilai & Hasil
Akademik dihitung terpisah di rencana awal jadi total 9) sudah
dibangun, diverifikasi tinker + UI, dan didokumentasikan. Gap lintas
domain yang masih terbuka:

- **Belum ada portal self-service** untuk dosen (input nilai/presensi
  sendiri) atau mahasiswa (lihat transkrip/KRS sendiri) — semua masih
  admin-only lewat Filament, gap yang konsisten dicatat di
  `docs/krs.md` dan `docs/perkuliahan.md`.
- **Belum ada pemicu otomatis** yang menjalankan
  `StudentSemesterResult::create()` di akhir tiap semester untuk semua
  mahasiswa aktif — masih dibuat satu-satu lewat Filament.
- Prioritas Fase 1 berikutnya sesuai blueprint (`#prioritas`) belum
  ditentukan — kandidat berikutnya di luar SIAKAD murni: Keuangan/UKT,
  atau memperdalam gap Prioritas 0-1 yang sudah dicatat (RBAC untuk
  Unit/Role/Permission, split role Dosen Pengampu/Wali, dll).
