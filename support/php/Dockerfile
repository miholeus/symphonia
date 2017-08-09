FROM php:7.0-fpm
MAINTAINER miholeus <public@miholeus.com>

RUN apt-get update && \
    apt-get install -y libmcrypt-dev libpq-dev vim net-tools libxml2 libxml2-dev

RUN docker-php-ext-install \
    mcrypt \
    bcmath \
    mbstring \
    zip \
    opcache \
    pdo pdo_pgsql pdo_mysql \
    soap

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

# Install mongo
RUN yes | pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongo.ini

# Install igbinary support
RUN yes | pecl install igbinary \
    && echo "extension=igbinary.so" > /usr/local/etc/php/conf.d/igbinary.ini

# Install redis
RUN yes | pecl install redis \
    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

COPY configs/fpm_www.conf /usr/local/etc/php-fpm.d/www.conf
COPY configs/php.ini      /usr/local/etc/php/php.ini
