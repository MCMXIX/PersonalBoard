#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage bootstrap/cache

if grep -q '^APP_KEY=$' .env; then
    php artisan key:generate --force
fi

php artisan package:discover --ansi

exec docker-php-entrypoint "$@"
