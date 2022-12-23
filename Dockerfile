FROM php:8.2.1RC1-fpm-bullseye

COPY --from=composer:2.5.0 /usr/bin/composer /usr/bin/composer

WORKDIR /usr/src/search_server

RUN apt update
RUN apt install -y --no-install-recommends git-all zip unzip

RUN docker-php-ext-install mysqli

CMD composer install && php -S 0.0.0.0:5000 public/index.php
