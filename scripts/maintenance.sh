#!/usr/bin/env bash
#
# Mode maintenance & housekeeping rutin Az-Zahra Digital Campus.
#
# Usage:
#   ./scripts/maintenance.sh on [pesan]   # aktifkan mode maintenance
#   ./scripts/maintenance.sh off          # matikan mode maintenance
#   ./scripts/maintenance.sh cleanup      # housekeeping rutin
set -euo pipefail

cd "$(dirname "$0")/.."

case "${1:-}" in
  on)
    MESSAGE="${2:-Sedang pemeliharaan, kembali sebentar lagi.}"
    php artisan down --secret="$(openssl rand -hex 16)" --retry=60
    echo "Maintenance mode ON. Pesan: $MESSAGE"
    ;;
  off)
    php artisan up
    echo "Maintenance mode OFF."
    ;;
  cleanup)
    echo "==> Membersihkan failed job lama (>30 hari)"
    php artisan queue:prune-failed --hours=720
    echo "==> Membersihkan token reset password kadaluarsa"
    php artisan auth:clear-resets
    echo "==> Optimasi cache"
    php artisan optimize
    echo "Cleanup selesai. (audit_logs TIDAK disentuh — append-only by design, lihat AuditLogPolicy.)"
    ;;
  *)
    echo "Usage: $0 {on [pesan]|off|cleanup}" >&2
    exit 1
    ;;
esac
