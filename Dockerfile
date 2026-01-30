FROM php:8.1-apache

# Matikan semua MPM dulu (biar bersih)
RUN a2dismod mpm_event mpm_worker || true

# Aktifkan MPM prefork (WAJIB untuk PHP mod_php)
RUN a2enmod mpm_prefork rewrite

# Install ekstensi PHP
RUN docker-php-ext-install pdo pdo_mysql

# Copy project ke Apache
COPY . /var/www/html/

# Permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
