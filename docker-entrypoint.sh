#!/bin/sh
set -e

# Ensure storage and cache are writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate APP_KEY if it doesn't exist
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Run migrations in the background or skip if already done
echo "Checking migrations..."
php artisan migrate --force --no-interaction &

# Link storage
php artisan storage:link --force || true

echo "Starting application..."
# Use PORT provided by Render
exec php artisan serve --host 0.0.0.0 --port ${PORT:-10000}
