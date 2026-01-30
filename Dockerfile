FROM php:8.1-apache

# Hapus paksa file modul yang menyebabkan bentrokan
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
    && rm -f /etc/apache2/mods-enabled/mpm_worker.load \
    && rm -f /etc/apache2/mods-available/mpm_event.load \
    && rm -f /etc/apache2/mods-available/mpm_worker.load

# Aktifkan mpm_prefork (wajib untuk PHP) dan rewrite
RUN a2enmod mpm_prefork rewrite

# Install ekstensi database
RUN docker-php-ext-install pdo pdo_mysql

# Copy file project
COPY . /var/www/html/

# Set owner
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# Jalankan Apache di foreground
CMD ["apache2-foreground"]
