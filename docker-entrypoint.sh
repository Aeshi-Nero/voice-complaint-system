#!/bin/sh
set -e

# Ensure storage and cache are writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate APP_KEY if it doesn't exist (crucial for Render)
if [ -z "$APP_KEY" ]; then
    echo "No APP_KEY found, generating one..."
    php artisan key:generate --show --no-interaction
fi

# Run migrations if database is ready
echo "Waiting for Database to be ready..."
php -r '
$max_retries = 10;
$retry_count = 0;
while ($retry_count < $max_retries) {
    try {
        $driver = getenv("DB_CONNECTION") ?: "mysql";
        $host = getenv("DB_HOST");
        $db   = getenv("DB_DATABASE");
        $user = getenv("DB_USERNAME");
        $pass = getenv("DB_PASSWORD");
        
        if (!$host) {
             echo "DB_HOST not set, skipping health check\n";
             exit(0);
        }

        $dsn = "$driver:host=$host;dbname=$db";
        if ($driver == "pgsql") {
            $dsn = "pgsql:host=$host;port=5432;dbname=$db";
        }
        
        $pdo = new PDO($dsn, $user, $pass);
        exit(0);
    } catch (PDOException $e) {
        $retry_count++;
        echo "Waiting for database connection... ($retry_count) " . $e->getMessage() . "\n";
        sleep(2);
    }
}
exit(1);
'

echo "Database is ready. Running migrations..."
php artisan migrate --force

# Link storage
php artisan storage:link --force || true

echo "Starting application..."
exec php artisan serve --host 0.0.0.0 --port ${PORT:-10000}
