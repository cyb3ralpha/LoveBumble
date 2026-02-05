FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Ensure Apache uses our .htaccess when mounted; volume will override during development
WORKDIR /var/www/html
