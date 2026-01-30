FROM php:8.1-apache

# Hapus paksa file load mpm_event dan mpm_worker jika ada
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_worker.load

# Aktifkan mpm_prefork dan rewrite secara eksplisit
RUN a2enmod mpm_prefork rewrite

# Install ekstensi PHP MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copy project
COPY . /var/www/html/

# Set permission agar Apache bisa baca file
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
