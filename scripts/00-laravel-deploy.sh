#!/usr/bin/env bash
set -e

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run database migrations
php artisan migrate --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache

# Create symbolic link for storage
php artisan storage:link
