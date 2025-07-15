# Laravel Dockerfile cho production
FROM php:8.2-fpm

# Cài đặt thư viện hệ thống
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    nginx supervisor && \
    docker-php-ext-install pdo_mysql mbstring zip exif gd

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục app
WORKDIR /var/www

# Copy source code vào container
COPY . .

# Cài các package PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Tạo storage link + cache Laravel
RUN php artisan config:clear && php artisan config:cache \
    && php artisan route:cache && php artisan view:cache \
    && php artisan storage:link

# Phân quyền thư mục
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www/storage

# Copy file cấu hình nginx
COPY deploy/nginx.conf /etc/nginx/sites-available/default

# Copy file cấu hình supervisor (để chạy cả php-fpm và nginx cùng lúc)
COPY deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]
