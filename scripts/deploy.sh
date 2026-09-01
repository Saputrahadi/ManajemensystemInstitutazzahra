#!/usr/bin/env bash
#
# Deploy Az-Zahra Digital Campus ke server. Jalankan dari root project di
# server tujuan (asumsi git remote & .env sudah dikonfigurasi sebelumnya).
#
# Usage: ./scripts/deploy.sh
set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> Menarik kode terbaru"
git pull --ff-only

echo "==> Install dependency PHP"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Install & build asset front-end"
npm ci
npm run build

echo "==> Menjalankan migrasi database"
php artisan migrate --force

echo "==> Membersihkan & membangun ulang cache"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:assets

echo "==> Restart queue worker"
php artisan queue:restart

echo "Deploy selesai."
