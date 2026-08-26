FROM php:8.2-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        git \
        unzip \
        libsqlite3-dev \
        libzip-dev \
    && docker-php-ext-install -j$(nproc) pdo_sqlite zip \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --ignore-platform-reqs --optimize-autoloader \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
    && chmod -R 777 storage bootstrap/cache database \
    && php -r "file_exists('vendor/autoload.php') || exit(1);"

ENV PORT=10000
EXPOSE 10000

CMD sh -c "php artisan migrate --force || true; php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"
