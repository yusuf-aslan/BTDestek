#!/bin/bash

# Ensure storage and bootstrap/cache are writable
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Create storage link if not exists
if [ ! -d "/var/www/public/storage" ]; then
    php artisan storage:link
fi

# Run migrations
php artisan migrate --force

exec "$@"
