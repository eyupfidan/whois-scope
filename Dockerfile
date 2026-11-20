# syntax=docker/dockerfile:1

FROM node:24-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# The composer image ships its own (newest) PHP; the runtime version is enforced by the app stage.
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-req=php

FROM php:8.6-fpm-alpine AS app

ARG WWWGROUP=1000
ARG WWWUSER=1000
# SQLite is built in. Space-separated PDO drivers to compile, e.g. "pdo_pgsql" or "pdo_mysql"; empty for SQLite only.
ARG PHP_DB_EXTENSIONS="pdo_pgsql"

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    RUN_MIGRATIONS=true

WORKDIR /var/www/html

RUN apk add --no-cache nginx supervisor \
    && apk add --no-cache --virtual .user-deps shadow \
    && groupmod -o -g "${WWWGROUP}" www-data \
    && usermod -o -u "${WWWUSER}" -g www-data www-data \
    && apk del .user-deps

RUN if [ -n "${PHP_DB_EXTENSIONS}" ]; then \
        apk add --no-cache --virtual .db-build-deps ${PHPIZE_DEPS} postgresql-dev \
        && docker-php-ext-install -j"$(nproc)" ${PHP_DB_EXTENSIONS} \
        && apk add --no-cache libpq \
        && apk del .db-build-deps; \
    fi \
    # Everything the app needs is built into the official image; fail early if that changes.
    && php -r 'foreach (["Zend OPcache","mbstring","pdo_sqlite","ctype","curl","dom","fileinfo","iconv","openssl","tokenizer"] as $e) { if (!extension_loaded($e)) { fwrite(STDERR, "missing extension: $e\n"); exit(1); } }'

COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/php/production.ini /usr/local/etc/php/conf.d/production.ini
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint

RUN chmod +x /usr/local/bin/docker-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan package:discover --ansi

EXPOSE 80

ENTRYPOINT ["docker-entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
