FROM php:8.4-fpm

RUN apt-get update && apt-get install -y unzip git curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli zip pdo_pgsql

RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
