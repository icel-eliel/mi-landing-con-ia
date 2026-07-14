FROM php:8.2-apache

# Instalar extensiones de PDO y MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite de Apache si es necesario
RUN a2enmod rewrite

# Establecer puerto por defecto para que funcione localmente o si no se define
ENV PORT=80

# Configurar Apache para usar la variable de entorno PORT provista por Railway
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

# Copiar archivos del proyecto al directorio público de Apache
COPY . /var/www/html/

# Asignar permisos correctos para el servidor web Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

