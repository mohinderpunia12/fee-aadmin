#!/bin/sh
set -e

echo "Starting FeeAdmin container..."

# Ensure correct permissions for storage and bootstrap/cache
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Wait for database to be ready (with timeout)
echo "Waiting for database connection..."
timeout=30
counter=0
until php artisan db:show &> /dev/null || [ $counter -eq $timeout ]; do
  echo "Database is unavailable - sleeping ($counter/$timeout)"
  sleep 1
  counter=$((counter + 1))
done

if [ $counter -eq $timeout ]; then
  echo "Warning: Database connection timeout, continuing anyway..."
fi

# Run migrations if RUN_MIGRATIONS=true
if [ "$RUN_MIGRATIONS" = "true" ]; then
  echo "Running database migrations..."
  php artisan migrate --force || echo "Migration failed, continuing..."
fi

# Create storage link if RUN_STORAGE_LINK=true
if [ "$RUN_STORAGE_LINK" = "true" ]; then
  echo "Creating storage link..."
  php artisan storage:link || echo "Storage link already exists or failed"
fi

# Clear and cache config (only in production)
if [ "$APP_ENV" = "production" ]; then
  echo "Caching configuration..."
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
fi

# Start PHP-FPM in background (daemon mode)
echo "Starting PHP-FPM..."
php-fpm -D

# Wait a moment for PHP-FPM to start
sleep 2

# Verify PHP-FPM is running
if ! pgrep -x php-fpm > /dev/null; then
  echo "ERROR: PHP-FPM failed to start!"
  exit 1
fi

# Verify nginx config
echo "Testing nginx configuration..."
nginx -t

# Start Nginx in foreground (this keeps container alive)
echo "Starting Nginx on 0.0.0.0:80..."
exec nginx -g 'daemon off;'

