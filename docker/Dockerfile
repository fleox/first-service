FROM php:8.1.0-fpm

ARG SYMFONY_ENV

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        zlib1g-dev \
        libxml2-dev \
        libpng-dev \
        libzip-dev \
        vim curl debconf subversion git apt-transport-https apt-utils \
        build-essential locales acl mailutils wget nodejs zip unzip \
        gnupg gnupg1 gnupg2 \
        nginx \
        supervisor \
        sudo \
        ssh

RUN docker-php-ext-install \
        pdo_mysql \
        soap \
        zip \
        opcache \
        gd \
        intl

RUN pecl install -o -f redis \
        &&  rm -rf /tmp/pear \
        &&  docker-php-ext-enable redis

COPY docker/build/php/opcache.ini /usr/local/etc/php/conf.d/
COPY docker/build/php/custom.ini /usr/local/etc/php/conf.d/

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN composer self-update 2.2.4

RUN wget --no-check-certificate https://phar.phpunit.de/phpunit-6.5.3.phar && \
        mv phpunit*.phar phpunit.phar && \
        chmod +x phpunit.phar && \
        mv phpunit.phar /usr/local/bin/phpunit

RUN usermod -u 1000 www-data
RUN usermod -a -G www-data root
RUN mkdir -p /var/www
RUN chown -R www-data:www-data /var/www
RUN mkdir -p /var/www/.composer
RUN chown -R www-data:www-data /var/www/.composer

WORKDIR /var/www/project/
COPY . .

RUN if [ "$SYMFONY_ENV" = "prod" ]; then set -eux; \
        mkdir -p var/cache var/log; \
        chown -R www-data:www-data var; \
        composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
        composer dump-autoload --classmap-authoritative --no-dev; \
        composer run-script --no-dev post-install-cmd; \
        chmod +x bin/console; fi

RUN if [ "$SYMFONY_ENV" = "staging" ] || [ "$SYMFONY_ENV" = "dev" ]; then set -eux; \
        mkdir -p var/cache var/log; \
        chown -R www-data:www-data var; \
        composer install --prefer-dist --no-progress --no-scripts --no-interaction; \
        chmod +x bin/console; sync; fi

RUN bin/console assets:install

COPY docker/build/nginx/default.conf /etc/nginx/sites-enabled/default
RUN echo "upstream php-upstream { server app_firstservice:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN mkdir -p /var/log/supervisor
COPY docker/build/supervisor /etc/supervisor

COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
