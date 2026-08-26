FROM php:8.2-cli-bookworm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --ignore-platform-reqs --no-interaction --no-autoloader

COPY . .
RUN composer dump-autoload --optimize --no-dev --ignore-platform-reqs \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
    && chmod -R 777 storage bootstrap/cache database \
    && test -f vendor/autoload.php

ENV PORT=10000
EXPOSE 10000
CMD ["sh", "-c", "php artisan migrate --force || true; exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
