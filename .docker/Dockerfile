FROM webdevops/php-nginx-dev:7.4-alpine

WORKDIR /app

RUN composer install

RUN php bin/console doctrine:migrations:migrate