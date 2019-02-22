FROM mysql:5.7

COPY ./mysqld_charset.cnf /etc/mysql/conf.d/mysqld_charset.cnf
COPY ./init/init.sql /docker-entrypoint-initdb.d/