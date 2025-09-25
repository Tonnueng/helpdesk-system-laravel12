#!/bin/bash

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Create storage directories
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Create database if not exists
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Create storage link
php artisan storage:link

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Start server
php artisan serve --host=0.0.0.0 --port=$PORT
