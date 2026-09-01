# Arsitektur — KRS (Fase 1, Prioritas 2 — SIAKAD)

## Tabel & model (`App\Models\Krs\*`)

| Tabel | Model | Peran |
|---|---|---|
| `krs` | `Krs` | Header Kartu Rencana Studi — satu per mahasiswa per tahun akademik + `term` |
| `krs_details` | `KrsDetail` | Kelas (`App\Models\Kelas\CourseClass`) yang diambil dalam satu KRS, `credits` di-snapshot otomatis |
| `krs_approvals` | `KrsApproval` | Log keputusan (disetujui/ditolak) — riwayat, bukan status tunggal, karena KRS bisa ditolak lalu diajukan ulang |

## Otomatisasi lewat model event, bukan Service

Berbeda dari domain PMB (yang punya `App\Services\Pmb\*` eksplisit), KRS
cukup sederhana sehingga logikanya ditaruh langsung di model event
(`static::booted()`), mengikuti pola `Auditable`/`SkCounter` yang sudah ada:

- **`KrsDetail::creating`** — kalau `credits` belum diisi, ambil dari
  `courseClass.courseOffering.course.credits` (snapshot SKS saat kelas
  diambil, supaya perubahan SKS mata kuliah di kemudian hari tidak mengubah
  riwayat KRS lama).
- **`KrsDetail::saved` / `::deleted`** — memanggil
  `Krs::recalculateTotalCredits()` (`updateQuietly`, tidak memicu event
  `Auditable` tambahan) supaya `krs.total_credits` selalu sinkron dengan
  jumlah SKS baris `krs_details` yang ada, tanpa perlu dihitung manual di
  form.
- **`KrsApproval::created`** — langsung `update()` `status` KRS induk
  mengikuti keputusan baris approval terbaru. Field `Status` di form
  `KrsResource` sengaja `disabled()` saat edit — hanya berubah lewat tab
  **Persetujuan**, bukan diedit langsung, supaya riwayat approval dan
  status KRS tidak pernah berbeda.

## Admin (Filament, navigation group "KRS")

`KrsResource` dengan dua relation manager: **Kelas Diambil**
(`DetailsRelationManager` — pilih `CourseClass`, SKS tampil otomatis,
tanpa Edit karena field itu hasil snapshot bukan input manual) dan
**Persetujuan** (`ApprovalsRelationManager` — catat keputusan baru, append-only
seperti `StatusHistoriesRelationManager` di domain Mahasiswa, tanpa
Edit/Delete). Digate `KrsPolicy` dan permission dari `KrsPermissionSeeder`:
`krs.view`, `krs.manage`, `krs.approve`.

## Verifikasi

Tinker: create `Krs` → tambah dua `KrsDetail` (3 SKS + 2 SKS, `total_credits`
otomatis jadi 5) → tambah `KrsApproval` `disetujui` (status KRS otomatis
ikut jadi `disetujui`) → hapus satu detail (`total_credits` otomatis
turun ke 2, snapshot detail lain tidak berubah). Semua dicek ulang dari DB
segar (`Krs::find()`), bukan cuma instance in-memory. UI Filament asli:
list & edit page `KrsResource` menampilkan `total_credits`/`status` yang
sama persis dengan hasil tinker, tab **Kelas Diambil** dan **Persetujuan**
menampilkan data yang benar.

## Yang masih terbuka

- **Belum ada portal self-service mahasiswa** — KRS diinput/dikelola lewat
  Filament admin, bukan lewat form yang diisi mahasiswa sendiri.
  `App\Livewire\Auth\Login` sudah menyiapkan login non-admin untuk "future
  Dosen/Mahasiswa portals" (lihat CLAUDE.md) tapi portal-nya sendiri belum
  dibangun — begitu ada, alur pengajuan KRS ini perlu dipindah/ditambah
  jadi form Livewire yang dipanggil mahasiswa, bukan cuma Filament.
- **Approver belum divalidasi harus Dosen Wali mahasiswa bersangkutan** —
  `ApprovalsRelationManager` menampilkan semua `User`, bukan difilter ke
  dosen dengan `lecturer_assignments.assignment_type = 'dosen_wali'` di
  Prodi yang sama dengan mahasiswa. Perlu domain Dosen Wali yang lebih
  matang (lihat gap yang sama di `docs/dosen.md`) sebelum ini bisa
  divalidasi otomatis.
- **Belum ada validasi max SKS** — pengambilan kelas di `krs_details`
  tidak dibatasi total SKS maksimum (mis. berbasis IPK semester
  sebelumnya) karena domain Nilai/Hasil Akademik belum dibangun.
- **Belum ada validasi bentrok jadwal** antar kelas yang diambil dalam satu
  KRS — berkaitan dengan gap yang sama yang dicatat di
  `docs/kelas-jadwal.md`.
