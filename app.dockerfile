FROM php:7.3-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev \
    mysql-client \
    && docker-php-ext-install pdo_mysql

EXPOSE 9000

CMD [ "php-fpm" ]