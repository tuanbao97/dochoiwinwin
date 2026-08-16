#!/usr/bin/env bash
# Script deploy cho server (Hostinger/cPanel/VPS).
# Dùng: bash deploy.sh
set -e

cd "$(dirname "$0")"

echo "==> Cài dependency"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Cập nhật database"
php artisan migrate --force --no-interaction

echo "==> Kiểm tra Passport key"
if [ ! -f storage/oauth-private.key ]; then
  php artisan passport:keys --force
fi

echo "==> Xóa cache"
php artisan optimize:clear

echo "==> Xong"
php artisan migrate:status
