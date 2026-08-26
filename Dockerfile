FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libsqlite3-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_sqlite zip mbstring bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# pehle sirf composer files
COPY composer.json composer.lock* ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# baaki code
COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && composer dump-autoload --optimize \
    && php artisan package:discover --ansi || true

ENV PORT=10000
EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=${PORT}
