FROM php:8.1-apache

# Install ekstensi database
RUN docker-php-ext-install pdo pdo_mysql

# Aktifkan rewrite untuk routing PHP
RUN a2enmod rewrite

# Copy project ke dalam folder web Apache
COPY . /var/www/html/

# Set permission agar web server bisa akses file
RUN chown -R www-data:www-data /var/www/html

# Jalankan skrip pembersihan modul tepat sebelum start
ENTRYPOINT ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_worker.load && a2enmod mpm_prefork && apache2-foreground"]

EXPOSE 80
