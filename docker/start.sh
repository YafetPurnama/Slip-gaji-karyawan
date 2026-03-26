#!/bin/sh
set -e

echo "========================================="
echo "  Starting Laravel Deployment Script"
echo "========================================="

cd /var/www/html

# Cache configuration for performance
echo "[1/5] Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "[2/5] Running database migrations..."
php artisan migrate --force

# Create storage symlink
echo "[3/5] Creating storage link..."
php artisan storage:link --force || true

# Clear any stale cache
echo "[4/5] Clearing stale cache..."
php artisan cache:clear || true

# Start services
echo "[5/5] Starting Nginx + PHP-FPM..."
echo "========================================="
echo "  Application is ready!"
echo "========================================="

exec /usr/bin/supervisord -c /etc/supervisord.conf
