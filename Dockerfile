FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock* ./
RUN composer install \
    --no-interaction \
    --no-progress \
    --prefer-dist

FROM php:8.4-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-install \
    intl \
    mbstring

COPY --from=vendor /app/vendor ./vendor
COPY . .

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "public/index.php"]
