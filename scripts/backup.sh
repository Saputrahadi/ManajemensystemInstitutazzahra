#!/usr/bin/env bash
#
# Backup database Az-Zahra Digital Campus. Baca kredensial langsung dari
# .env (DB_CONNECTION, DB_DATABASE, dst.) supaya tidak duplikasi config.
#
# Usage: ./scripts/backup.sh [output-dir]
set -euo pipefail

cd "$(dirname "$0")/.."

ENV_FILE=".env"
[ -f "$ENV_FILE" ] || { echo "$ENV_FILE tidak ditemukan." >&2; exit 1; }

env_var() { grep -E "^${1}=" "$ENV_FILE" | head -n1 | cut -d '=' -f2- | tr -d '"'; }

DB_CONNECTION=$(env_var DB_CONNECTION)
OUT_DIR="${1:-storage/app/backups}"
mkdir -p "$OUT_DIR"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

case "$DB_CONNECTION" in
  pgsql)
    DB_DATABASE=$(env_var DB_DATABASE)
    DB_HOST=$(env_var DB_HOST)
    DB_USERNAME=$(env_var DB_USERNAME)
    DB_PASSWORD=$(env_var DB_PASSWORD)
    OUT_FILE="$OUT_DIR/${DB_DATABASE}_${TIMESTAMP}.sql.gz"
    echo "==> Backup database PostgreSQL '$DB_DATABASE' -> $OUT_FILE"
    PGPASSWORD="$DB_PASSWORD" pg_dump -h "$DB_HOST" -U "$DB_USERNAME" "$DB_DATABASE" | gzip > "$OUT_FILE"
    ;;
  sqlite)
    DB_DATABASE=$(env_var DB_DATABASE)
    OUT_FILE="$OUT_DIR/database_${TIMESTAMP}.sqlite"
    echo "==> Menyalin database SQLite -> $OUT_FILE"
    cp "$DB_DATABASE" "$OUT_FILE"
    ;;
  *)
    echo "Driver '$DB_CONNECTION' belum didukung script ini." >&2
    exit 1
    ;;
esac

echo "Backup selesai: $OUT_FILE"
