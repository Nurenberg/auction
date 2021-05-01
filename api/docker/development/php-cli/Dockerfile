FROM php:7.4-fpm

ENV XDEBUG_VERSION 3.0.3

RUN apt-get update && apt-get install -y wget git unzip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini


COPY ./development/php/conf.d /usr/local/etc/php/conf.d

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

WORKDIR /app