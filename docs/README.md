# Dokumentasi — Az-Zahra Digital Campus

Dokumentasi teknis dan SOP, mencakup Fase 0 (Core System) dan modul Fase 1
yang sudah mulai dibangun. Untuk gambaran fitur & cara pakai singkat, lihat
[README.md](../README.md) di root project.

- [architecture.md](architecture.md) — arsitektur RBAC, data-scoping per Unit,
  Audit Trail, dan struktur modul Core System (Fase 0).
- [pmb.md](pmb.md) — domain PMB (Fase 1, Prioritas 1) — selesai end-to-end,
  pendaftaran publik sampai keputusan & registrasi ulang.
- [mahasiswa.md](mahasiswa.md) — domain Mahasiswa (Fase 1, Prioritas 2 —
  SIAKAD) — konversi Applicant → Student otomatis.
- [dosen.md](dosen.md) — domain Dosen (Fase 1, Prioritas 2 — SIAKAD) —
  akun + data dosen dibuat langsung oleh admin.
- [kurikulum.md](kurikulum.md) — domain Kurikulum (Fase 1, Prioritas 2 —
  SIAKAD) — kurikulum, mata kuliah, prasyarat, CPL/CPMK.
- [kelas-jadwal.md](kelas-jadwal.md) — domain Kelas & Jadwal (Fase 1,
  Prioritas 2 — SIAKAD) — pembukaan mata kuliah, kelas paralel, dosen
  pengampu, jadwal & ruang.
- [krs.md](krs.md) — domain KRS (Fase 1, Prioritas 2 — SIAKAD) — Kartu
  Rencana Studi, snapshot SKS otomatis, alur persetujuan.
- [perkuliahan.md](perkuliahan.md) — domain Perkuliahan (Fase 1, Prioritas
  2 — SIAKAD) — pertemuan kuliah, presensi, materi, tugas & pengumpulan.
- [ujian.md](ujian.md) — domain Ujian/UTS-UAS (Fase 1, Prioritas 2 —
  SIAKAD) — jadwal ujian, peserta, pengumpulan jawaban & skor.
- [nilai.md](nilai.md) — domain Nilai (Fase 1, Prioritas 2 — SIAKAD) —
  komponen nilai, kalkulasi otomatis nilai akhir, alur koreksi nilai.
- [hasil-akademik.md](hasil-akademik.md) — domain Hasil Akademik (Fase 1,
  Prioritas 2 — SIAKAD, penutup) — IP/IPK otomatis, transkrip, kelulusan.
- [keuangan.md](keuangan.md) — domain Keuangan + BSI (Fase 1, Prioritas
  3) — tagihan, Virtual Account, webhook callback BSI (teruji lewat curl
  nyata), perpanjangan jatuh tempo, reminder otomatis.
- [sistem-dosen.md](sistem-dosen.md) — Sistem Dosen (Fase 1, Prioritas 4)
  — portal self-service dosen (Livewire, bukan Filament), Dosen Wali &
  Bimbingan.
- [sop/setup-local.md](sop/setup-local.md) — SOP setup environment
  development lokal, termasuk masalah yang sudah pernah ditemui.
- [sop/deployment.md](sop/deployment.md) — SOP deploy ke server, backup
  database, dan mode maintenance, mengacu ke script di `../scripts/`.

Dokumen ini akan bertambah seiring modul baru (Akademik, Keuangan, dst.)
dibangun pada fase berikutnya — setiap modul sebaiknya punya halaman
arsitektur sendiri di folder ini, mengikuti pola `architecture.md`/`pmb.md`
yang ada.
