FROM php:8.1.17-apache

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip && docker-php-ext-install zip

RUN apt-get install -y git

RUN docker-php-ext-install mysqli

RUN docker-php-ext-enable mysqli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer require firebase/php-jwt vlucas/phpdotenv

# https://getcomposer.org/download/
# https://github.com/firebase/php-jwt
# https://github.com/vlucas/phpdotenv
