#!/bin/bash

# Ensure storage directories exist and are writable
mkdir -p storage/framework/{sessions,views,cache}
chmod -R 775 storage bootstrap/cache

# Run standard optimization
php artisan config:clear
php artisan cache:clear

if [ "$APP_ENV" = "production" ]; then
    echo "Running in Production (Hugging Face)"
    
    # Initialize storage and start PHP
    php artisan storage:link --force || true
    php-fpm -D
    
    # Install nginx if not present
    if ! command -v nginx &> /dev/null
    then
        apt-get update && apt-get install -y nginx
    fi
    
    # Apply our custom config to the system
    cp /var/www/nginx/default.conf /etc/nginx/sites-available/default
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
    
    echo "Launching Nginx on Port 7860..."
    exec nginx -g "daemon off;"
else
    echo "Running in Development (Local)"
    php artisan storage:link --force || true
    exec php-fpm
fi
