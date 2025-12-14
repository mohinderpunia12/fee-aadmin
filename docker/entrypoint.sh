#!/bin/sh
set -e

# Wait for database to be ready (with timeout)
echo "Waiting for database..."
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

# Create storage link if it doesn't exist
php artisan storage:link || true

# Clear and cache config (only in production)
if [ "$APP_ENV" = "production" ]; then
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
fi

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

