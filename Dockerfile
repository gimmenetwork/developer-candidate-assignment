FROM webdevops/php-nginx-dev:7.4-alpine

WORKDIR /app

COPY gimmemore /app

RUN composer install