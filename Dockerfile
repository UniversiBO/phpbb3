FROM php:5.6-apache

RUN apt-get update &&\
    apt-get install -y\
        libpq-dev\
        libicu-dev\
        zlib1g-dev\
        git\
        &&\
    docker-php-ext-install\
        pdo\
        pdo_pgsql\
        pgsql\
        intl\
        zip\
        calendar

RUN a2enmod rewrite
