FROM php:7.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql
