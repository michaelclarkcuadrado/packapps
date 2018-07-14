# packapps dockerization

FROM php:apache
#get around crazy php image bug that changes the workdir
WORKDIR /var/www  
RUN docker-php-ext-install mysqli pdo pdo_mysql
#RUN apt-get update; apt-get install zbar-tools -y //TODO deprecate barcode scanning

RUN a2enmod rewrite
RUN a2enmod headers

#PHP debugging, disable for deploy
RUN pecl install xdebug && docker-php-ext-enable xdebug

EXPOSE 80

COPY src /var/www/
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY config/php.ini /usr/local/etc/php/

