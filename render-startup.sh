#!/bin/sh

# Exit immediately if a command exits with a non-zero status
set -e

echo "Running database migrations..."
php artisan migrate --force

if [ "$SCOUT_DRIVER" = "algolia" ]; then
    echo "Importing properties to Algolia index..."
    php artisan scout:import "App\Models\Property"
fi

echo "Starting Apache..."
exec apache2-foreground
