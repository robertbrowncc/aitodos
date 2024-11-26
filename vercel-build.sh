#!/bin/bash

echo "Starting Vercel build process..."

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm install
npm run build

# Create SQLite database
touch /tmp/database.sqlite

# Run migrations and seed the database
php artisan migrate --force
php artisan db:seed --force

echo "Build process completed!"
