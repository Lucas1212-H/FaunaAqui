# Multi-stage build for Laravel backend
# Builder: install PHP dependencies via Composer
FROM composer:2 AS builder
WORKDIR /app
COPY composer.json composer.lock ./
COPY . /app
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

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

# Render-friendly startup: cache (best effort), migrate (best effort), then serve on PORT
CMD ["sh","-lc","php artisan config:cache || true && php artisan route:cache || true && php artisan view:cache || true && php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=${PORT}"]
