FROM php:8.2-apache

RUN apt-get update \
 && apt-get install -y git zlib1g-dev mariadb-client libzip-dev \
 nodejs \
 npm 

RUN docker-php-ext-install zip mysqli pdo_mysql

RUN a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && echo 'ServerName localhost' >> /etc/apache2/apache2.conf 


RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/public
COPY . .

# Copy list of PHP dependencies and install every dependency of this list
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer
RUN composer dump-autoload --no-scripts --no-dev --optimize

# Copy list of node dependencies and install every dependency of this list
RUN npm install

# Create a non-root user for Apache
RUN useradd -r -u 1001 -g www-data apacheuser
# Set directory permissions as needed
RUN chown -R :www-data /var/www/public