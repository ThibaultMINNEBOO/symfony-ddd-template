FROM composer:2 as builder

WORKDIR /app

COPY ../composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-progress --optimize-autoloader

COPY .. /app

FROM dunglas/frankenphp:builder-php8.5-bookworm as base

ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV SERVER_NAME=localhost
ENV TZ=Europe/Paris

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
    lsb-release ca-certificates curl gnupg libpq-dev libicu-dev libzip-dev unzip supervisor

RUN install-php-extensions \
    pgsql \
    pdo_pgsql \
    sqlite3 \
    pdo_sqlite \
    gd \
    opcache \
    intl \
    zip \
    tokenizer \
    ctype \
    iconv

WORKDIR /app

COPY --from=builder /app/docker/messenger-worker.conf /etc/supervisor/conf.d/messenger-worker.conf

COPY --from=builder /app /app

FROM base as dev

RUN chmod +x /app/docker/docker.sh

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

ENTRYPOINT ["./docker/docker.sh"]

EXPOSE 80
EXPOSE 443
