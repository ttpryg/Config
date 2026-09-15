ARG PHP_VERSION=8.1

FROM php:${PHP_VERSION}-fpm

RUN apt-get update && apt-get install -y \
    bash \
    git \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    intl \
    opcache \
    pdo_mysql \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./

RUN composer install \
    --no-interaction \
    --no-scripts \
    --prefer-dist

COPY . .

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
