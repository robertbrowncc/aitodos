#!/bin/bash

echo "Starting Vercel build process..."

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Clear any existing node_modules and package-lock.json
rm -rf node_modules package-lock.json

# Install Node dependencies and build assets
export NODE_OPTIONS="--max_old_space_size=4096"
npm install
npm run build

# Create SQLite database
touch /tmp/database.sqlite

# Run migrations and seed the database
php artisan migrate --force
php artisan db:seed --force

echo "Build process completed!"
