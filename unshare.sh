#!/bin/bash

# Check if backup exists
if [ ! -f .env.backup ]; then
    echo "Error: No .env.backup file found"
    exit 1
fi

# Restore original .env
echo "Restoring original .env..."
mv .env.backup .env

echo "Restored original .env file"

# Clear Laravel caches
echo "Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run npm build
echo "Running npm run build..."
npm run build

echo "Unshare complete! Your app is now restored to its original configuration."
