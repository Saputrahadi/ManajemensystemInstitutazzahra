# Arsitektur — Nilai (Fase 1, Prioritas 2 — SIAKAD)

## Tabel & model (`App\Models\Nilai\*`)

| Tabel | Model | Peran |
|---|---|---|
| `grade_components` | `GradeComponent` | Komponen penilaian per Kelas (mis. Tugas 30%, UTS 30%, UAS 40%), `weight` dalam persen |
| `grades` | `Grade` | Nilai mentah mahasiswa per komponen — belum dikalikan bobot |
| `grade_finalizations` | `GradeFinalization` | Nilai akhir per mahasiswa per Kelas — `final_score`/`letter_grade`/`grade_point` hasil kalkulasi |
| `grade_change_requests` | `GradeChangeRequest` | Permintaan koreksi atas satu `grade_finalization` yang sudah terbit |
| `grade_change_approvals` | `GradeChangeApproval` | Riwayat keputusan (disetujui/ditolak) atas satu `grade_change_request` |

## Kalkulasi otomatis, bukan input manual

`GradeFinalization::booted()` (`creating`) menghitung `final_score` dari
`GradeFinalization::computeWeightedScore($classId, $studentId)` — jumlah
`score` tiap `GradeComponent` milik kelas itu dikali `weight/100` — **kalau
`final_score` tidak diisi di form**. Form `GradeFinalizationResource`
sengaja tidak menyertakan field `final_score`/`letter_grade`/`grade_point`
sama sekali, jadi selalu dihitung otomatis saat baris pertama kali dibuat.

Skala huruf mutu → angka mutu (`GradeFinalization::SCALE`, method
`scoreToScale()`/`pointForLetter()`):

| Skor | Huruf | Angka |
|---|---|---|
| ≥ 85 | A | 4.00 |
| ≥ 80 | AB | 3.50 |
| ≥ 75 | B | 3.00 |
| ≥ 70 | BC | 2.50 |
| ≥ 65 | C | 2.00 |
| ≥ 60 | CD | 1.50 |
| ≥ 55 | D | 1.00 |
| < 55 | E | 0.00 |

Tombol **Hitung Ulang** di tabel `GradeFinalizationResource` memanggil
ulang `computeWeightedScore()` + `scoreToScale()` dan menimpa baris yang
ada — berguna kalau `grades`/`grade_components` berubah setelah
finalisasi pertama.

**Catatan implementasi**: semua nilai `decimal` (`final_score`,
`grade_point`) di-cast eksplisit ke `string` sebelum di-assign ke
attribute Eloquent (`(string) $score`, bukan `$score` float langsung) —
`brick/math` (dipakai Laravel untuk cast `decimal:N`) sudah men-deprecate
menerima float mentah di `BigNumber::of()`. Ketahuan lewat warning nyata
di tinker saat verifikasi awal, bukan dari review kode statis.

## Alur koreksi nilai (mengikuti pola KRS)

1. `GradeChangeRequest` dibuat lewat tab **Permintaan Koreksi** di
   `GradeFinalizationResource`, status default `menunggu`.
2. Diputuskan lewat tab **Persetujuan** di `GradeChangeRequestResource`
   (resource top-level terpisah, karena `GradeChangeApproval` adalah anak
   dari `GradeChangeRequest` — dua level di bawah `GradeFinalization`,
   sehingga tidak bisa jadi relation manager bersarang di
   `GradeFinalizationResource` langsung).
3. `GradeChangeApproval::created` (append-only, sama seperti
   `KrsApproval`) meng-update `status` `GradeChangeRequest` induknya, dan
   kalau `disetujui`, langsung menimpa `final_score`/`letter_grade`
   (dari nilai yang diminta) + `grade_point` (dihitung ulang dari
   `requested_letter_grade` lewat `pointForLetter()`, **bukan** dari
   `requested_score`, supaya huruf dan angka mutu tetap konsisten
   walau dosen memilih huruf secara diskresioner, tidak persis
   mengikuti skala numerik).

## Admin (Filament, navigation group "Nilai")

- `GradeComponentResource` — relation manager **Nilai Mahasiswa**
  (`GradesRelationManager`, input skor per mahasiswa per komponen).
- `GradeFinalizationResource` — relation manager **Permintaan Koreksi**
  (`ChangeRequestsRelationManager`) + action tabel **Hitung Ulang**.
- `GradeChangeRequestResource` — relation manager **Persetujuan**
  (`ApprovalsRelationManager`).

Digate lewat Policy (`GradeComponentPolicy`, `GradeFinalizationPolicy`,
`GradeChangeRequestPolicy`) dan permission dari `NilaiPermissionSeeder`:
`nilai.component.{view,manage}`, `nilai.finalization.{view,manage}`,
`nilai.change.approve`. `Grade`/`GradeChangeApproval` tidak punya Policy
sendiri, ikut permission resource induknya lewat relation manager — pola
yang sama dipakai domain Kurikulum/Kelas & Jadwal/Perkuliahan/Ujian.

## Verifikasi

Tinker: 3 `GradeComponent` (Tugas 30%, UTS 30%, UAS 40%) dengan skor
90/80/85 → `GradeFinalization` otomatis terhitung `85.00` / `A` / `4.00`
(dicek manual: 90×0.3 + 80×0.3 + 85×0.4 = 27+24+34 = 85, cocok persis) →
`GradeChangeRequest` (minta `88.00`/`A`) → `GradeChangeApproval`
`disetujui` → `GradeFinalization` ter-update jadi `88.00`/`A`/`4.00`.
Ditemukan & diperbaiki warning deprecation `brick/math` saat verifikasi
awal (lihat catatan implementasi di atas), diverifikasi ulang bersih
tanpa warning setelah fix. UI Filament asli: list `GradeFinalizationResource`
menampilkan `88.00` (state pasca-approval) → klik **Hitung Ulang** →
berubah balik jadi `85.00` (state hasil komponen) dengan konfirmasi
modal yang benar; `GradeChangeRequestResource` edit page menampilkan tab
**Persetujuan** dengan riwayat yang benar.

## Yang masih terbuka

- **Belum ada validasi total bobot `grade_components` = 100%** per kelas —
  admin bisa membuat komponen yang jumlah bobotnya kurang/lebih dari 100
  tanpa penolakan.
- **Belum terhubung ke domain Nilai lanjutan (Hasil Akademik)** — IPK per
  semester/kumulatif, transkrip, dan syarat kelulusan belum dibangun;
  `grade_point` di sini baru per mata kuliah, belum diagregasi.
- Skor dari `Perkuliahan.assignments`/`Ujian.exams` belum otomatis
  masuk ke `grades` — masih input manual terpisah, meski secara konsep
  komponen "Tugas"/"UTS"/"UAS" di sini merepresentasikan hal yang sama.
