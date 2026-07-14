FROM php:8.2-cli

RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html

WORKDIR /var/www/html

RUN chmod +x /var/www/html/start.sh

EXPOSE 80

ENTRYPOINT ["/bin/sh", "-c", "/var/www/html/start.sh"]
