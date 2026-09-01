# SOP — Deployment, Backup & Maintenance

Script pendukung ada di [`scripts/`](../../scripts/): `deploy.sh`,
`backup.sh`, `maintenance.sh`. Semua diasumsikan dijalankan dari root
project di server (Linux), lewat SSH.

## Deploy

```bash
./scripts/deploy.sh
```

Urutan yang dilakukan: `git pull` → `composer install --no-dev` →
`npm ci && npm run build` → `migrate --force` → rebuild cache
(`config`/`route`/`view`) → `filament:assets` → `queue:restart`.

Untuk deploy pertama kali di server baru, lakukan setup manual dulu
(clone repo, isi `.env`, `key:generate`) sebelum menjalankan script ini —
script ini untuk **update**, bukan instalasi awal.

## Backup database

```bash
./scripts/backup.sh                      # default: storage/app/backups/
./scripts/backup.sh /path/lain           # atau tentukan folder tujuan
```

Script membaca `DB_CONNECTION` dari `.env` secara otomatis — mendukung
`pgsql` (lewat `pg_dump`, di-gzip) dan `sqlite` (disalin langsung).
Jadwalkan lewat cron, mis. tiap malam jam 2:

```cron
0 2 * * * cd /path/ke/project && ./scripts/backup.sh >> storage/logs/backup.log 2>&1
```

**Catatan penting**: backup ini untuk disaster recovery, bukan pengganti
`audit_logs`. Tabel `audit_logs` sengaja append-only (lihat
[docs/architecture.md](../architecture.md#audit-trail)) dan tidak boleh
"dibersihkan" lewat backup/restore parsial.

## Mode maintenance & housekeeping rutin

```bash
./scripts/maintenance.sh on "Update sistem, kembali 15 menit lagi"
./scripts/maintenance.sh off
./scripts/maintenance.sh cleanup   # failed jobs lama, token reset kadaluarsa, optimize cache
```

`cleanup` aman dijalankan rutin lewat cron (mis. mingguan) — tidak
menyentuh `audit_logs` maupun data domain lain.
