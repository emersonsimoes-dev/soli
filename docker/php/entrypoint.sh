#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chmod -R ug+rwx storage bootstrap/cache || true

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ -f .env ] && grep -q '^APP_KEY=$' .env; then
    php artisan key:generate --force --no-interaction
fi

exec "$@"
