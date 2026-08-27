FROM php:8.4-cli-bookworm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql zip

WORKDIR /app
COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
    && chmod -R 777 storage bootstrap/cache database

ENV PORT=10000
EXPOSE 10000
CMD ["sh", "-c", "php artisan config:clear || true; php artisan cache:clear || true; php artisan migrate --force --no-interaction 2>&1 || true; exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
