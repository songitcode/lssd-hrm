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
COPY . /var/www

# Cấp quyền cho Laravel
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Chạy Composer install với www-data
USER www-data
RUN composer install --no-dev --optimize-autoloader

# Copy .env và reset quyền
USER root
COPY .env.example /var/www/.env
RUN chown www-data:www-data /var/www/.env

# Quay lại www-data để chạy lệnh artisan
USER www-data
RUN php artisan key:generate

RUN php artisan config:clear && php artisan config:cache
RUN php artisan migrate --force || true  

# Mở port
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]