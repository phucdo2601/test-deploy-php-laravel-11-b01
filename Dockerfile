# Use the official PHP-FPM image
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Copy Laravel project files
WORKDIR /var/www/html
COPY . .

# Ensure required directories exist
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 10000 (Render default)
EXPOSE 8223

# Start PHP-FPM and Nginx
CMD service php8.2-fpm start && nginx -g 'daemon off;'
