FROM php:8.2-fpm

WORKDIR /var/www

# Cài extension PHP
RUN apt-get update && apt-get install -y \
    zip unzip curl git libxml2-dev libzip-dev libpng-dev libjpeg-dev libonig-dev \
    sqlite3 libsqlite3-dev && \
    docker-php-ext-install pdo pdo_mysql exif pcntl bcmath gd zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy mã nguồn & phân quyền
COPY --chown=www-data:www-data . /var/www

RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Cài composer với quyền www-data
USER www-data
RUN composer install --optimize-autoloader --no-dev

# Copy .env và generate key
COPY .env.example .env
RUN php artisan key:generate

# Cache lại config
RUN php artisan config:clear && php artisan config:cache

# Migrate database
RUN php artisan migrate --force

USER root

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]