# PHP + Apache
FROM php:8.1-apache

# Install extension yang dibutuhkan
RUN docker-php-ext-install pdo pdo_mysql

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Copy semua file project ke Apache
COPY . /var/www/html/

# Set permission
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
