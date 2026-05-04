#!/bin/bash

# Force DB connection to mysql if not set (to prevent sqlite 500 error)
export DB_CONNECTION=${DB_CONNECTION:-mysql}
export SESSION_DRIVER=${SESSION_DRIVER:-cookie}
export BROADCAST_CONNECTION=${BROADCAST_CONNECTION:-log}

# Ensure storage directories exist and are writable
mkdir -p storage/framework/{sessions,views,cache}
chmod -R 775 storage bootstrap/cache

if [ "$APP_ENV" = "production" ]; then
    echo "Running in Production (Hugging Face) - Applying Speed Optimizations..."
    
    # Initialize storage
    php artisan storage:link --force || true
    
    # Run migrations automatically
    echo "Syncing cloud database..."
    php artisan migrate --force || true
    
    # SPEED OPTIMIZATIONS:
    # -------------------
    echo "Pre-compiling application logic..."
    php artisan config:cache   # Merge all configs
    php artisan route:cache    # Pre-load all URLs
    php artisan view:cache     # Pre-compile all UI templates
    
    php-fpm -D
    
    # Install nginx if not present
    if ! command -v nginx &> /dev/null
    then
        apt-get update && apt-get install -y nginx
    fi
    
    # Apply our custom config to the system
    cp /var/www/nginx/default.conf /etc/nginx/sites-available/default
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
    
    echo "Launching Optimized V.O.I.C.E. on Port 7860..."
    exec nginx -g "daemon off;"
else
    echo "Running in Development (Local)"
    # Clear caches in local to prevent dev issues
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan storage:link --force || true
    exec php-fpm
fi
