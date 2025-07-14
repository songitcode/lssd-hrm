FROM php:8.2-fpm

WORKDIR /var/www

# Cài extension
RUN apt-get update && apt-get install -y \
    zip unzip curl git libxml2-dev libzip-dev libpng-dev libjpeg-dev libonig-dev \
    sqlite3 libsqlite3-dev && \
    docker-php-ext-install pdo pdo_mysql exif pcntl bcmath gd zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy mã nguồn và set quyền
COPY --chown=www-data:www-data . /var/www

# Cấp quyền đúng cho Laravel
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Cài thư viện PHP dưới quyền www-data (tránh lỗi ghi file)
USER www-data
RUN composer install

RUN php artisan config:clear && php artisan config:cache

# Quay lại root user
USER root

# Tạo file .env và key
COPY .env.example .env
RUN php artisan key:generate

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]