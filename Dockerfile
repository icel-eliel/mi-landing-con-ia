FROM php:8.2-cli

RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html

WORKDIR /var/www/html

EXPOSE 80

# Usamos la sintaxis de shell de CMD para que se ejecute bajo /bin/sh y expanda $PORT automáticamente
CMD php -S 0.0.0.0:$PORT -t .
