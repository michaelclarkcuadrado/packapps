# packapps dockerization

FROM php:apache
#get around crazy php image bug that changes the workdir
WORKDIR /var/www  
RUN docker-php-ext-install mysqli pdo pdo_mysql
#RUN apt-get update; apt-get install zbar-tools -y
# mod_pagespeed is not compatible with a reverse SSL proxy
#RUN cd /tmp \
#    && curl -o /tmp/mod-pagespeed.deb https://dl-ssl.google.com/dl/linux/direct/mod-pagespeed-stable_current_amd64.deb \
#    && dpkg -i /tmp/mod-pagespeed.deb \
#    && apt-get -f install
RUN a2enmod rewrite
RUN a2enmod headers

EXPOSE 80

COPY src /var/www/
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY config/php.ini /usr/local/etc/php/

