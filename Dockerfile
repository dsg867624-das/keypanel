FROM php:8.4-cli-bookworm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_sqlite zip

WORKDIR /app
COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
    && touch database/database.sqlite \
    && chmod -R 777 storage bootstrap/cache database

ENV PORT=10000
EXPOSE 10000
CMD ["sh", "-c", "touch /app/database/database.sqlite; chmod 666 /app/database/database.sqlite; php artisan migrate --force --path=database/migrations/2026_08_21_122812_create_keys_table.php; php artisan migrate --force || true; exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
