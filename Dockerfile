FROM php:8.4-fpm-alpine

RUN apk add --no-cache icu-dev libzip-dev unzip \
    && docker-php-ext-install intl pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts

COPY . .
COPY docker/entrypoint.sh /usr/local/bin/personalboard-entrypoint

RUN chmod +x /usr/local/bin/personalboard-entrypoint artisan \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["personalboard-entrypoint"]
CMD ["php-fpm"]
