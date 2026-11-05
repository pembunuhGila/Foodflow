#!/bin/bash
set -e

# ========================================================
# Script Build & Deploy FoodFlow di Server AWS EC2
# ========================================================

APP_DIR="/var/www/Foodflow"
cd "$APP_DIR"

echo ">>> [1/6] Mengambil kode terbaru dari GitHub..."
CURRENT_BRANCH=$(git branch --show-current)
git pull origin "${CURRENT_BRANCH:-main}"

echo ">>> [2/6] Menginstall PHP Dependencies (Composer)..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo ">>> [3/6] Membuild Aset Frontend (Vite / Tailwind / Alpine)..."
npm install --no-audit --no-fund
npm run build

echo ">>> [4/6] Menjalankan Database Migration di Supabase..."
php artisan migrate --force

echo ">>> [5/6] Mengoptimasi Cache Laravel (Config, Route, View)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">>> [6/6] Menyesuaikan Hak Akses Folder Storage & Cache..."
sudo chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

sudo systemctl reload nginx
sudo systemctl restart php8.4-fpm

echo "========================================================"
echo " DEPLOYMENT BERHASIL! APLIKASI TELAH AKTIF DI AWS."
echo "========================================================"
