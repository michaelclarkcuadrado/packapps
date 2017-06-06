# packapps dockerization

FROM php:apache
WORKDIR /var/www  #get around crazy php image bug
RUN docker-php-ext-install mysqli
RUN apt-get update; apt-get install zbar-tools -y

COPY src /var/www/
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY config/php.ini /usr/local/etc/php/

EXPOSE 80
