#!/usr/bin/env bash
# Script deploy cho server (Hostinger/cPanel/VPS).
# Dùng: bash deploy.sh
set -e

cd "$(dirname "$0")"

echo "==> Cài dependency"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Cập nhật database"
php artisan migrate --force --no-interaction

echo "==> Seed dữ liệu nền (tài khoản, phân quyền, cấu hình, danh mục)"
php artisan deploy:seed --no-interaction

echo "==> Kiểm tra Passport key"
if [ ! -f storage/oauth-private.key ] || [ ! -f storage/oauth-public.key ]; then
  php artisan passport:keys --force || {
    echo "artisan passport:keys không chạy được, tạo key bằng openssl"
    openssl genrsa -out storage/oauth-private.key 4096
    openssl rsa -in storage/oauth-private.key -pubout -out storage/oauth-public.key
    chmod 600 storage/oauth-private.key 2>/dev/null || true
    chmod 660 storage/oauth-public.key 2>/dev/null || true
  }
fi

echo "==> Xóa cache"
php artisan optimize:clear

echo "==> Kiểm tra Sapo"
php artisan sapo:status --ping || true

echo "==> Xong"
php artisan migrate:status
