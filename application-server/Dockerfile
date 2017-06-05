# packapps dockerization

FROM php:apache
RUN docker-php-ext-install mysqli

COPY src /var/www/
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY config/php.ini /usr/local/etc/php/

EXPOSE 80
