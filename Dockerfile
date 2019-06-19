FROM php:fpm-alpine

WORKDIR /opt/treehole

RUN apk update && apk add composer postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

COPY composer.json .
COPY composer.lock .
COPY database database
COPY tests tests

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

COPY . .

CMD php -S 0.0.0.0:8000 -t public
