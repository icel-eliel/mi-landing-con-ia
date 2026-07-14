FROM php:8.2-cli

WORKDIR /var/www/html

# Instalar extensiones de PDO y MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copiar archivos del proyecto
COPY . /var/www/html/

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]
