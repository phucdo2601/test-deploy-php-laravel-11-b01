FROM php:8.2-fpm

WORKDIR /var/www/html

# Copy the custom php.ini to the appropriate path
COPY ./php.ini /usr/local/etc/php/php.ini

# Install necessary system dependencies
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
    libonig-dev \
    libxml2-dev \
    jpegoptim optipng pngquant gifsicle

# Configure and install the GD extension with WebP, JPEG, and FreeType support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli curl gd mbstring

# Install  Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
