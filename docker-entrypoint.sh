#!/bin/sh
set -e

# Run migrations if database is ready
echo "Waiting for MySQL to be ready..."
until php artisan db:monitor --databases=mysql > /dev/null 2>&1; do
  sleep 1
done

echo "MySQL is ready. Running migrations..."
php artisan migrate --force

# Run npm install and build if build/manifest.json is missing
if [ ! -f "public/build/manifest.json" ]; then
    echo "Building assets..."
    npm install
    npm run build
fi

echo "Starting PHP-FPM..."
exec php-fpm
