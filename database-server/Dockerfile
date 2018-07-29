# packapps-db dockerization

FROM mariadb:10.3

COPY config/my.cnf /etc/mysql/my.cnf
ENV MYSQL_ROOT_PASSWORD="packapps"

#TODO development setting
EXPOSE 3306:3306

COPY schema/* /docker-entrypoint-initdb.d/
