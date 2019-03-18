FROM mysql:5.7

ADD create.sql /docker-entrypoint-initdb.d
