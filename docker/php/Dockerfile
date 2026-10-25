FROM php:8.5.12-fpm-alpine

ARG DEV_UID=1000
ARG DEV_GID=1000

RUN apk add --no-cache su-exec shadow bash postgresql-dev linux-headers \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath sockets

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Set PHP timezone and charset
RUN echo "date.timezone = America/Caracas" > /usr/local/etc/php/conf.d/timezone.ini \
    && echo "default_charset = \"UTF-8\"" >> /usr/local/etc/php/conf.d/charset.ini

# create group/user at build time
RUN addgroup -g ${DEV_GID} devgroup \
 && adduser -D -u ${DEV_UID} -G devgroup devuser

# Configure PHP-FPM to run as devuser
RUN sed -i 's/user = www-data/user = devuser/' /usr/local/etc/php-fpm.d/www.conf \
 && sed -i 's/group = www-data/group = devgroup/' /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
