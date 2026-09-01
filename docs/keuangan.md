# Arsitektur — Keuangan + BSI (Fase 1, Prioritas 3)

Prioritas 3 di blueprint (`#prioritas`) menggabungkan dua modul peta
organisasi: **"03. Keuangan & Pembayaran"** dan **"04. Integrasi Bank
BSI"** — dijalankan sebagai satu domain sesuai urutan pembangunan resmi
(`Prioritas 0` Fondasi → `Prioritas 1` PMB → `Prioritas 2` SIAKAD →
**`Prioritas 3` Keuangan + BSI** → `Prioritas 4` Sistem Dosen → ...).
Alur intinya persis seperti disebut di blueprint: *"NIM → Tagihan UKT →
BSI → Pembayaran → Callback → Lunas → Rekonsiliasi → Laporan"*, plus
otomasi reminder H-7/H-3/H-1, jatuh tempo → status overdue, dan
perpanjangan → approval pimpinan → deadline baru.

## Tabel & model (`App\Models\Keuangan\*`)

| Tabel | Model | Peran |
|---|---|---|
| `fee_types` | `FeeType` | Jenis Tagihan (UKT, PMB, registrasi, wisuda, layanan, beasiswa, dispensasi) |
| `fee_tariffs` | `FeeTariff` | Master Tarif — nominal per Jenis Tagihan, opsional dipersempit per Prodi + tahun akademik |
| `payment_document_counters` | `PaymentDocumentCounter` | Counter row-locked untuk penomoran invoice/kwitansi — pola sama dengan `SkCounter`/`NimCounter` |
| `invoices` | `Invoice` | Tagihan Mahasiswa (menyatukan langkah "Tagihan" + "Invoice" di alur blueprint jadi satu dokumen) |
| `virtual_accounts` | `VirtualAccount` | Virtual Account BSI, satu per Invoice |
| `payments` | `Payment` | Pembayaran — dari callback BSI (otomatis) atau verifikasi manual admin |
| `payment_callbacks` | `PaymentCallback` | Log mentah tiap callback masuk dari BSI (Transaction Log/Error Handling) |
| `receipts` | `Receipt` | Kwitansi — diterbitkan otomatis begitu satu Payment berhasil |
| `due_date_extensions` | `DueDateExtension` | Pengajuan perpanjangan jatuh tempo |
| `due_date_extension_approvals` | `DueDateExtensionApproval` | Keputusan pimpinan atas satu pengajuan — riwayat append-only |
| `payment_reminders` | `PaymentReminder` | Log reminder H-7/H-3/H-1 yang sudah dikirim, mencegah kirim dobel |

## Kredensial BSI belum tersedia — bagaimana ini tetap diverifikasi nyata

`.env.example` sudah mereservasi `BSI_CLIENT_ID`/`BSI_CLIENT_SECRET`/
`BSI_PARTNER_ID`/`BSI_BASE_URL` sejak Fase 0, tapi kredensial sandbox
sungguhan belum didaftarkan — jadi **VA number dibentuk deterministik**
(`config('bsi.company_code')` + ID invoice, di-zero-pad — lihat
`App\Models\Keuangan\VirtualAccount::booted()`), bukan hasil panggilan
API "Generate VA" BSI yang sesungguhnya. Begitu kredensial nyata ada,
tinggal ganti titik pembuatan VA itu dengan panggilan API — struktur
data & alur callback-nya sudah benar sekarang.

Yang **sungguh-sungguh nyata dan teruji** adalah arah sebaliknya: webhook
inbound `POST /api/bsi/callback` (`App\Http\Controllers\Keuangan\Api\BsiCallbackController`,
diautentikasi `App\Http\Middleware\VerifyBsiCallbackToken` lewat header
`X-Callback-Token` dicocokkan ke `BSI_CALLBACK_TOKEN` pakai `hash_equals`).
Ini diverifikasi dengan benar-benar meng-curl dev server yang jalan
(bukan cuma tinker) — men-simulasikan panggilan BSI ke sistem, persis
seperti bank sungguhan akan memanggilnya:

```bash
curl -X POST http://127.0.0.1:8010/api/bsi/callback \
  -H "Content-Type: application/json" \
  -H "X-Callback-Token: <BSI_CALLBACK_TOKEN>" \
  -d '{"virtual_account_number":"...","amount":3500000,"paid_at":"2026-08-24T10:00:00Z"}'
```

Token salah → `401`. Token benar + VA valid → `200` dan seluruh rantai
efek samping berjalan (lihat bagian berikutnya). Callback dobel untuk
invoice yang sudah lunas **diabaikan secara idempoten** (dicek &
diverifikasi lewat curl dua kali berturut-turut) — bank pembayaran nyata
sering mengirim callback lebih dari sekali untuk transaksi yang sama.

## Satu titik logika "pembayaran terverifikasi"

`App\Services\Keuangan\PaymentVerificationService::verify()` dipanggil
dari **dua jalur** — `BsiCallbackService` (otomatis) dan aksi
"Verifikasi" manual di `PaymentResource`/`InvoiceResource`'s
`PaymentsRelationManager` (untuk pembayaran tunai/transfer manual di
luar VA) — supaya kedua jalur tidak pernah berbeda perilaku. Efeknya:

1. `Payment.status` → `berhasil`
2. `Invoice.status` → `lunas`
3. `Receipt` diterbitkan otomatis (nomor dari `DocumentNumberService`)
4. **Pivot Alur Inti Mahasiswa**: kalau `FeeType.category === 'ukt'` dan
   `Student.student_status === 'registrasi'`, otomatis berubah jadi
   `'aktif'` — persis seperti dicontohkan blueprint bagian "Alur Inti
   Mahasiswa" (NIM 27010001, "Belum Registrasi Akademik" → lunas UKT →
   "Aktif" tanpa entri manual).
5. Notifikasi `GenericNotification` ke mahasiswa.

Diverifikasi end-to-end: mahasiswa dibuat dengan `student_status =
'registrasi'` → tagihan UKT dibuat → callback BSI disimulasikan via curl
→ `student_status` berubah jadi `'aktif'` (dicek lewat tinker **dan**
UI Filament `StudentResource` — badge status menampilkan "aktif").

## "Human Decision, System Execution" — perpanjangan jatuh tempo

Blueprint punya contoh eksplisit: *"Pimpinan menetapkan: 'Pembayaran UKT
diperpanjang sampai 30 Agustus.' Sistem otomatis menjalankan: ubah
tanggal jatuh tempo, update tagihan, kirim notifikasi, ..."* — ini
diimplementasikan literal di `DueDateExtensionApproval::booted()`
(`created`, pola sama dengan `KrsApproval`/`GradeChangeApproval`):
sekali baris persetujuan `disetujui` dibuat, `Invoice.due_date` langsung
berubah ke `requested_due_date` **dan** mahasiswa langsung dapat
`GenericNotification` — bukan cuma catat status "disetujui" lalu
menunggu proses lain.

Alur lengkap: diajukan lewat tab **Perpanjangan Jatuh Tempo** di
`InvoiceResource` → diputuskan lewat tab **Persetujuan** di
`DueDateExtensionResource` (resource top-level terpisah, karena
`DueDateExtensionApproval` adalah anak dari `DueDateExtension` — dua
level di bawah `Invoice`, pola yang sama dengan
`GradeChangeRequestResource` di domain Nilai).

## Otomasi terjadwal (Artisan command, belum di-cron)

- `php artisan keuangan:mark-overdue` — `App\Services\Keuangan\InvoiceOverdueService`,
  menandai `Invoice` yang `due_date`-nya sudah lewat + status masih
  `belum_bayar` jadi `overdue`, kirim notifikasi.
- `php artisan keuangan:send-reminders` — `App\Services\Keuangan\PaymentReminderService`,
  mengirim reminder H-7/H-3/H-1 (dicek terhadap `payment_reminders` biar
  tidak dobel — diverifikasi dengan menjalankan command yang sama dua
  kali berturut-turut: kali kedua mengirim 0 reminder).

Belum dijadwalkan lewat cron OS di environment dev ini (tidak ada daemon
scheduler jalan) — siap dipasang ke Laravel Scheduler produksi nanti,
logikanya sudah lengkap & teruji lewat command manual.

## Admin (Filament, navigation group "Keuangan")

Delapan resource: `FeeTypeResource`, `FeeTariffResource`, `InvoiceResource`
(relation manager Pembayaran + Perpanjangan Jatuh Tempo — nomor invoice
& VA dibuat otomatis lewat `InvoiceService`, lihat `Pages\CreateInvoice`),
`DueDateExtensionResource` (relation manager Persetujuan),
`PaymentResource` (tampilan global lintas invoice, aksi "Verifikasi"),
`ReceiptResource`/`PaymentCallbackResource`/`PaymentReminderResource`
(ketiganya **tanpa Create** — murni log otomatis, pola sama dengan
`StudentCumulativeResultResource` di domain Hasil Akademik).

Digate lewat 9 Policy dan permission dari `KeuanganPermissionSeeder`:
`keuangan.fee_type.{view,manage}`, `keuangan.tariff.{view,manage}`,
`keuangan.invoice.{view,manage}`, `keuangan.payment.{view,verify}`,
`keuangan.extension.{view,manage,approve}` — `Receipt`/`PaymentCallback`/
`PaymentReminder` memakai permission `.view` yang sudah ada (bukan bikin
kode permission baru untuk tabel log read-only), `DueDateExtensionApproval`
tidak punya Policy sendiri (ikut lewat relation manager, pola konsisten
seluruh sesi ini).

## Verifikasi

**Tinker**: fee type UKT → tarif Rp 3.500.000 → `InvoiceService::createForStudent()`
(invoice `INV/2026/00001` + VA `7777700000000001` sekaligus, dalam satu
transaksi) → nomor invoice kedua otomatis `INV/2026/00002` (counter
row-locked bekerja). **Curl nyata** ke webhook: callback pertama →
`Invoice` lunas, `Payment` berhasil, `Receipt` `KWT/2026/00001` terbit,
`Student.student_status` `registrasi` → `aktif`, notifikasi terkirim;
callback kedua (VA sama) → diabaikan idempoten, jumlah `Payment` tetap
1. Jalur manual (non-VA) diverifikasi terpisah lewat
`PaymentVerificationService::verify()` langsung — `Receipt`
`KWT/2026/00002` terbit dengan nomor urut benar. Perpanjangan jatuh
tempo: due_date `2026-08-29` → diajukan `2026-09-23` → disetujui →
`Invoice.due_date` benar-benar berubah + notifikasi terkirim. Kedua
Artisan command dijalankan nyata (`keuangan:mark-overdue`,
`keuangan:send-reminders`, yang kedua dijalankan dua kali untuk
membuktikan idempotensi).

**UI Filament asli**: seluruh 8 resource dicek lewat klik browser
sungguhan — list `InvoiceResource` menampilkan status
`overdue`/`belum_bayar`/`lunas` yang sama persis dengan tinker/curl; tab
Pembayaran & Perpanjangan Jatuh Tempo menampilkan data yang benar;
`PaymentResource`, `ReceiptResource`, `PaymentCallbackResource` (baris
callback duplikat dengan pesan error yang benar), `PaymentReminderResource`
semua menampilkan hasil yang identik; `StudentResource` mengonfirmasi
badge status mahasiswa benar-benar berubah jadi "aktif" di UI, bukan
cuma di database.

## Yang masih terbuka

- **Kredensial BSI sandbox/production belum ada** — VA number simulasi,
  belum pernah dites terhadap API BSI sungguhan (lihat penjelasan di
  atas). Rekonsiliasi/Settlement resmi BSI (laporan mutasi harian dari
  bank) belum diimplementasikan — `payment_callbacks` saat ini menjadi
  sumber rekonsiliasi de facto, bukan proses rekonsiliasi terpisah.
- **Belum ada Artisan Scheduler / cron OS** yang menjalankan
  `keuangan:mark-overdue` dan `keuangan:send-reminders` otomatis setiap
  hari — masih manual, siap dipasang begitu ada environment produksi
  dengan scheduler daemon jalan.
- **Belum ada "Laporan"** (laporan keuangan agregat/piutang) sebagai
  halaman Filament tersendiri — data mentahnya (invoices, payments)
  sudah lengkap untuk itu, tapi belum dibungkus jadi dashboard/laporan.
  Kandidat alami untuk Prioritas 6 (Dashboard Pimpinan) di blueprint.
- **Belum ada validasi jumlah bayar vs tagihan** — `BsiCallbackService`
  menerima `amount` apa pun dari payload tanpa mencocokkan ke
  `Invoice.amount`; pembayaran kurang/lebih bayar tetap langsung
  menandai lunas.
- Gap SQLite/`brick-math` yang sama dengan domain Hasil Akademik
  berlaku di sini juga (`Payment.amount_paid`, `Invoice.amount`,
  `FeeTariff.amount`, dst.) — sudah ditangani proaktif dengan
  `getRawOriginal()` di titik-titik arithmetic (`PaymentVerificationService`,
  `BsiCallbackService`), lihat `docs/hasil-akademik.md` untuk penjelasan
  akar masalahnya.
