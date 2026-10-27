# PHP 8.2.35 fixes CVE-2026-109618 and CVE-2026-109619.
# Update the version and multi-platform digest together for future security releases.
FROM php:8.2.35-apache@sha256:1dcd13aa67697127a140a7d303ab955c923098c39d525ecd728f2e8afb4d1546

ARG USER="regmon"
ARG UID="1000"

USER root

RUN useradd -r --no-log-init --create-home --shell /bin/bash --uid "$UID" --user-group "$USER"

RUN apt-get update \
 && apt-get install -y git zlib1g-dev mariadb-client libzip-dev \
 nodejs \
 npm

RUN docker-php-ext-install zip mysqli pdo_mysql

RUN pecl config-set php_ini "${PHP_INI_DIR}/php.ini"
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && echo 'ServerName localhost' >> /etc/apache2/apache2.conf 

RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/public

COPY composer.json composer.json
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer
RUN composer dump-autoload --no-scripts --no-dev --optimize

COPY package.json package.json
RUN npm install

USER ${USER}
