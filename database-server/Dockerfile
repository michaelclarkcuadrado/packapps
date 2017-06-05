# packapps-db dockerization

FROM mariadb:10.2

COPY config/my.cnf /etc/mysql/my.cnf
COPY schema.sql /docker-entrypoint-initdb.d
COPY configure.sql /docker-entrypoint-initdb.d
ENV MYSQL_ROOT_PASSWORD="packapps"

#TODO development setting
EXPOSE 3306:3306
