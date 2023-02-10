ARG dependency_proxy_prefix="library"
FROM mlocati/php-extension-installer:2.0.2 AS php-extension-installer
FROM $dependency_proxy_prefix/php:8.2-alpine

LABEL maintainer="Umbrellio"
SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

COPY --from=php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    redis

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer \

RUN mkdir exporter
WORKDIR /exporter
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --quiet

COPY ./ ./

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

