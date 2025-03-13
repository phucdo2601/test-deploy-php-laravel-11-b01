# Use the official PHP image with FPM
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    unzip \
    git \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-xml \
    php8.2-mbstring \
    php8.2-bcmath \
    php8.2-curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www/html

# Copy Laravel project files
COPY . .

# Ensure the required directories exist
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 10000 for Render
EXPOSE 10000

# Start PHP-FPM and Nginx correctly
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
