FROM php:8.4-fpm

# Install system dependencies, nginx, and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl nginx libpng-dev libonig-dev libxml2-dev \
    libpq-dev libzip-dev zip unzip nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets
RUN npm ci && npm run build && rm -rf node_modules

# Create required Laravel directories and set permissions
RUN mkdir -p storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# PHP OPcache configuration
RUN echo "opcache.enable=1\nopcache.memory_consumption=128\nopcache.max_accelerated_files=4000\nopcache.validate_timestamps=0" \
    > /usr/local/etc/php/conf.d/opcache.ini

# Nginx configuration — routes all PHP requests to PHP-FPM via TCP socket
RUN printf 'server {\n\
    listen ${PORT:-80};\n\
    root /var/www/html/public;\n\
    index index.php index.html;\n\
\n\
    # Serve static files directly; fall back to index.php\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    # Pass PHP scripts to PHP-FPM\n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
\n\
    # Deny access to hidden files\n\
    location ~ /\\.(?!well-known).* {\n\
        deny all;\n\
    }\n\
}\n' > /etc/nginx/sites-available/default

# Startup script: configure port, run Laravel bootstrap tasks, then start services
RUN printf '#!/bin/bash\n\
set -e\n\
\n\
# Substitute the Railway-provided PORT into the nginx config\n\
sed -i "s/\${PORT:-80}/${PORT:-80}/" /etc/nginx/sites-available/default\n\
\n\
# Laravel bootstrap\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan migrate --force\n\
php artisan storage:link 2>/dev/null || true\n\
\n\
# Start PHP-FPM in the background\n\
php-fpm --daemonize\n\
\n\
# Start nginx in the foreground\n\
exec nginx -g "daemon off;"\n\
' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]