# Multi-stage build for Laravel backend
# Builder: install PHP dependencies via Composer
FROM composer:2 AS builder
WORKDIR /app
COPY composer.json composer.lock ./

# COPIAR o código antes, mas usando a flag --no-scripts para o composer não quebrar sem as pastas do Laravel
COPY . /app
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Runtime: PHP com a versão atualizada para 8.4 para satisfazer as dependências do Composer
FROM php:8.4-cli
WORKDIR /app

# Instala dependências do sistema, extensões PHP e o Python3
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libzip-dev zip unzip libpng-dev libonig-dev libxml2-dev git curl python3 \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif bcmath pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy application from builder (includes vendor)
COPY --from=builder /app /app

# Garantir que as pastas existam E tenham a permissão correta ANTES de qualquer comando
RUN mkdir -p /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Dá permissão de execução para o seu script de inicialização
RUN chmod +x /app/start-mysql.sh

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=8080

EXPOSE 8080

# Executa o comando via shell string para garantir a expansão correta das variáveis de ambiente e roda o script
CMD sh -c "php artisan package:discover --ansi || true && php artisan config:cache --no-interaction || true && php artisan route:cache --no-interaction || true && php artisan view:cache --no-interaction || true && php artisan migrate --force || true && php artisan storage:link --force || true && php artisan serve --host=0.0.0.0 --port=${PORT}"