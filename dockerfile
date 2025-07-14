FROM php:8.2-fpm

WORKDIR /var/www

# Cài extension
RUN apt-get update && apt-get install -y \
    zip unzip curl git libxml2-dev libzip-dev libpng-dev libjpeg-dev libonig-dev \
    sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite exif pcntl bcmath gd zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy source code
COPY --chown=www-data:www-data . /var/www

# Fix permissions
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Tạo .env và cấu hình
COPY .env.example .env
RUN chown www-data:www-data .env

# Chạy Composer và Artisan dưới www-data
USER www-data
RUN composer install --no-interaction --no-dev
RUN php artisan key:generate
RUN php artisan config:clear && php artisan config:cache

# Chạy server
USER root
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--no-reload"]