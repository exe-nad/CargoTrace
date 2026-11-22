#!/bin/sh
set -e

echo "[CargoTrace] Starting container boot sequence..."

# 1. Prepare Environment File
if [ ! -f /var/www/html/.env ]; then
    echo "[CargoTrace] Copying .env.example to .env..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# 2. Generate Application Key if not set
if ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "[CargoTrace] Generating application encryption key..."
    php artisan key:generate --force
fi

# 3. Setup SQLite Database
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "[CargoTrace] Initializing SQLite database file..."
    touch /var/www/html/database/database.sqlite
fi

chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database

# 4. Run Migrations & Realistic Dataset Seeder
echo "[CargoTrace] Executing database migrations..."
php artisan migrate --force

echo "[CargoTrace] Seeding CargoTrace operational dataset..."
php artisan db:seed --class=CargoTraceSeeder --force

# 5. Optimize Laravel Caches for Production
echo "[CargoTrace] Optimizing framework caches for high-speed delivery..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 6. Ensure Storage Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "[CargoTrace] Launching PHP-FPM and Nginx..."
php-fpm -D
exec nginx -g "daemon off;"
