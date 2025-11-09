# Simple container for running Laravel via artisan serve (demo/deploy)
FROM php:8.2-cli

# Install system deps and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files and install (prefer-dist, no dev for production)
COPY composer.json composer.lock /app/
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader || true

# Copy application code
COPY . /app

# Ensure storage and cache directories are writable
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Optional caches (ignore failures if env not yet set)
RUN php artisan config:clear || true && php artisan cache:clear || true && php artisan view:clear || true

# Start Laravel's built-in server (exec via shell to expand $PORT provided by Render)
CMD ["sh","-lc","php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]