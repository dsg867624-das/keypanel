FROM composer:2 AS builder
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --prefer-dist --ignore-platform-reqs --no-interaction

FROM php:8.2-cli-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends \
    libsqlite3-0 libzip4 unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_sqlite || true

WORKDIR /app
COPY --from=builder /app/vendor /app/vendor
COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && php -r "file_exists('vendor/autoload.php') || exit(1);"

ENV PORT=10000
EXPOSE 10000
CMD sh -c "php artisan serve --host=0.0.0.0 --port=\${PORT}"
