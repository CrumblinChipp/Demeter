#!/bin/bash
set -e

# Cache Laravel config, routes, and views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations automatically on deploy
php artisan migrate --force

# Create storage symlink for file uploads (campus maps)
php artisan storage:link --force 2>/dev/null || true

# Start Apache
exec "$@"
