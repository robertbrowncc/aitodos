#!/bin/bash

# Prompt for the share URL
echo "Please paste your Herd expose URL:"
read SHARE_URL

if [ -z "$SHARE_URL" ]; then
    echo "Error: No URL provided"
    exit 1
fi

echo "Using URL: $SHARE_URL"

# Backup the original .env file
cp .env .env.backup

# Replace only the APP_URL line in .env
sed -i '' '/^APP_URL=/s#=.*#='$SHARE_URL'#' .env

echo "Updated APP_URL in .env to: $SHARE_URL"

# Clear Laravel caches
echo "Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run npm build
echo "Running npm run build..."
npm run build

echo "Build complete! Your app is now accessible at: $SHARE_URL"

# Generate QR code
echo "Opening QR code in Safari..."
open -a Safari "https://qrcode.show/$SHARE_URL"

echo "Note: To restore your original .env, run: mv .env.backup .env"
