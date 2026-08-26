FROM php:8.2-cli-bookworm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_MEMORY_LIMIT=-1
WORKDIR /app
COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --ignore-platform-reqs --no-scripts \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
    && chmod -R 777 storage bootstrap/cache database

ENV PORT=10000
EXPOSE 10000
CMD ["sh", "-c", "php artisan migrate --force || true; exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
