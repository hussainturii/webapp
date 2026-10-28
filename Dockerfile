FROM php:8.1-apache
RUN apt-get update && apt-get install -y default-mysql-client
RUN docker-php-ext-install pdo pdo_mysql mysqli
COPY src/index.php /var/www/html/index.php
EXPOSE 80
