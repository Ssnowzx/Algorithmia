FROM php:8.4-fpm-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
    && docker-php-ext-install pdo_pgsql intl mbstring opcache zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY php.ini /usr/local/etc/php/conf.d/99-platform.ini
COPY entrypoint.sh /usr/local/bin/platform-entrypoint

RUN chmod +x /usr/local/bin/platform-entrypoint

WORKDIR /var/www/platform

ENTRYPOINT ["platform-entrypoint"]
CMD ["php-fpm"]
