FROM php:8.2-apache

# Instalar extensiones de PDO y MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite de Apache si es necesario
RUN a2enmod rewrite

# Copiar archivos del proyecto al directorio publico de Apache
COPY . /var/www/html/

# Asignar permisos correctos para el servidor web Apache
RUN chown -R www-data:www-data /var/www/html

COPY railway-apache-start.sh /usr/local/bin/railway-apache-start.sh
RUN sed -i 's/\r$//' /usr/local/bin/railway-apache-start.sh \
    && chmod +x /usr/local/bin/railway-apache-start.sh

EXPOSE 8080

CMD ["railway-apache-start.sh"]
