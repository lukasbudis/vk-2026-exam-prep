FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev
RUN apt-get install nano

RUN docker-php-ext-install gd pdo pdo_mysql

WORKDIR /var/www/html

COPY ./src/php-app /var/www/html

EXPOSE 80
