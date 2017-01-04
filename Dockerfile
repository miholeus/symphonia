FROM php:7.0-fpm
MAINTAINER miholeus <public@miholeus.com>

RUN apt-get update && \
    apt-get install -y libmcrypt-dev libpq-dev vim net-tools

RUN docker-php-ext-install \
    mcrypt \
    bcmath \
    mbstring \
    zip \
    opcache \
    pdo pdo_pgsql

RUN yes | pecl install xdebug-beta \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

RUN echo " \n\
xdebug.remote_enable=on \n\
xdebug.remote_autostart=on \n\
xdebug.remote_connect_back=on \n\
" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN yes | pecl install apcu \
    && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini \
    && echo "apc.enable_cli=1" >> /usr/local/etc/php/conf.d/apcu.ini

COPY support/php/fpm_www.conf /usr/local/etc/php-fpm.d/www.conf
COPY . /app/

WORKDIR /app

