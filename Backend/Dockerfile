# Multi-stage build for Laravel backend
# Builder: install PHP dependencies via Composer
FROM composer:2 AS builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
COPY . /app

# Runtime: PHP with extensions
FROM php:8.1-cli
WORKDIR /app

# Install system dependencies and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libzip-dev zip unzip libpng-dev libonig-dev libxml2-dev git curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif bcmath pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy application from builder (includes vendor)
COPY --from=builder /app /app

# Ensure storage and cache directories exist and have proper permissions
RUN mkdir -p /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache || true

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=8080

EXPOSE 8080

# On startup: try migrations (non-blocking), cache configs and start artisan server
CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && /usr/bin/supervisord -c /etc/supervisord.conf
