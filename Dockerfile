FROM php:8.2-apache

# Instalar extensiones de PDO y MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Usar un solo MPM de Apache. php:apache funciona con mpm_prefork.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* \
    && a2enmod mpm_prefork rewrite

# Railway puede omitir el CMD si hay un Start Command personalizado.
# Dejamos Apache listo en 8080 desde la imagen para evitar $PORT literal.
RUN sed -i 's/^Listen .*/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf \
    && apache2ctl -t

# Copiar archivos del proyecto al directorio publico de Apache
COPY . /var/www/html/

# Asignar permisos correctos para el servidor web Apache
RUN chown -R www-data:www-data /var/www/html

COPY railway-apache-start.sh /usr/local/bin/railway-apache-start.sh
RUN sed -i 's/\r$//' /usr/local/bin/railway-apache-start.sh \
    && chmod +x /usr/local/bin/railway-apache-start.sh

EXPOSE 8080

CMD ["apache2-foreground"]
