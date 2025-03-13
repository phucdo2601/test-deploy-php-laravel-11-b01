# Use PHP 8.2 FPM
FROM php:8.2-fpm

# Set working directory inside container
WORKDIR /var/www/html

# Copy the custom php.ini
COPY ./dockerfiles/php.ini /usr/local/etc/php/php.ini

# Install necessary system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
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
    jpegoptim optipng pngquant gifsicle \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli curl gd mbstring \
    && pecl install xdebug && docker-php-ext-enable xdebug

# Copy Laravel application (from src/)
COPY src/ ./

# Set correct permissions for Laravel storage & cache
RUN chmod -R 777 storage bootstrap/cache

# Expose port (Render needs this)
EXPOSE 10000

# Start PHP-FPM and Nginx
CMD service nginx start && php-fpm
