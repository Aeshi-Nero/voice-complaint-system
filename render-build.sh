#!/usr/bin/env bash
# exit on error
set -o errexit

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Install npm dependencies and build assets
npm install
npm run build

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (force because we're in production)
php artisan migrate --force
