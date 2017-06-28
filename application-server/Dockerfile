# packapps dockerization

FROM php:apache
#get around crazy php image bug
WORKDIR /var/www  
RUN docker-php-ext-install mysqli
# COPY config/jessie-backports.list /etc/apt/sources.list.d/jessie-backports.list
# RUN apt-get update; apt-get install python-certbot-apache -t jessie-backports
RUN apt-get update; apt-get install zbar-tools -y
RUN cd /tmp \
    && curl -o /tmp/mod-pagespeed.deb https://dl-ssl.google.com/dl/linux/direct/mod-pagespeed-stable_current_amd64.deb \
    && dpkg -i /tmp/mod-pagespeed.deb \
    && apt-get -f install
COPY src /var/www/
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY config/php.ini /usr/local/etc/php/



EXPOSE 80
