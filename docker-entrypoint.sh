#!/bin/sh
set -e

# Ensure storage and cache are writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Run migrations if database is ready
echo "Waiting for MySQL to be ready..."
php -r '
$max_retries = 30;
$retry_count = 0;
while ($retry_count < $max_retries) {
    try {
        $host = getenv("DB_HOST") ?: "mysql";
        $db   = getenv("DB_DATABASE") ?: "voice_db";
        $user = getenv("DB_USERNAME") ?: "voice_user";
        $pass = getenv("DB_PASSWORD") ?: "dbpassword";
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        exit(0);
    } catch (PDOException $e) {
        $retry_count++;
        echo "Waiting for database connection... ($retry_count) " . $e->getMessage() . "\n";
        sleep(2);
    }
}
exit(1);
'

echo "MySQL is ready. Running migrations..."
php artisan migrate --force

echo "Starting PHP-FPM..."
exec php-fpm
