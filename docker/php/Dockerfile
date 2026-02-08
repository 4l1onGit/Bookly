FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        dom \
        xml \
        intl \
        mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN mkdir -p /var/www/html/var/cache && \
    chown -R www-data:www-data /var/www/html/var
