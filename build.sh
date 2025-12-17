#!/bin/bash

echo "🚀 Building Laravel for Vercel..."

# Install PHP dependencies
composer install --optimize-autoloader --no-dev --no-interaction

# Create necessary directories
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Clear and cache config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!"