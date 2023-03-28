FROM php:8.1.17-apache

RUN docker-php-ext-install mysqli

RUN docker-php-ext-enable mysqli
