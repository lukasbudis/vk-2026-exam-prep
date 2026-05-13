# Použijeme oficiálny PHP-FPM image
FROM php:8.3-fpm

# Nainštalujeme potrebné rozšírenia
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        && docker-php-ext-install gd pdo pdo_mysql

# Nastavíme pracovný adresár
WORKDIR /var/www/html

# Kopírujeme zdrojový kód do kontajnera
COPY ./src/php-app /var/www/html

# Nastavíme správne vlastníctvo a oprávnenia
RUN chown -R www-data:www-data /var/www/html

# Expozícia portu (nie je nevyhnutná, ale môže byť užitočná)
EXPOSE 9000

CMD ["php-fpm"]