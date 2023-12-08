FROM php:8.3.0-alpine

ENV APP_ENV=local

WORKDIR /app

RUN apk add -Uuv \
    git bash freetype-dev libjpeg-turbo-dev libzip-dev \
    libpng-dev postgresql-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

COPY ./laravel /app

RUN ["chmod", "-R", "0777", "storage"]
RUN ["chmod", "-R", "0777", "bootstrap/cache"]
