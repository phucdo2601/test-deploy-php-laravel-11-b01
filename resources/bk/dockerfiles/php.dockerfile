FROM php:8.2-fpm

WORKDIR /var/www/html

# Copy the custom php.ini to the appropriate path
COPY ./php.ini /usr/local/etc/php/php.ini


# Install necessary system dependencies, including libcurl
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    autoconf \
    build-essential \
    libpng-dev \
    libwebp-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    libonig-dev \
    libxml2-dev

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_mysql mysqli curl gd mbstring