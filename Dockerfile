# Imagen base con PHP 8.2 y Apache
FROM php:8.2-apache

# Copiar todos los archivos del proyecto al contenedor
COPY . /var/www/html/

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql
