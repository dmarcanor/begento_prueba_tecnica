FROM php:8.5-fpm-alpine

ARG DEV_UID=1000
ARG DEV_GID=1000

RUN apk add --no-cache su-exec shadow bash postgresql-dev linux-headers \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath sockets

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Set PHP timezone and charset
RUN echo "date.timezone = America/Caracas" > /usr/local/etc/php/conf.d/timezone.ini \
    && echo "default_charset = \"UTF-8\"" >> /usr/local/etc/php/conf.d/charset.ini

# create group/user at build time (skip if GID/UID already exists)
RUN if ! getent group ${DEV_GID} >/dev/null; then addgroup -g ${DEV_GID} appgroup; fi \
 && if ! getent passwd ${DEV_UID} >/dev/null; then adduser -D -u ${DEV_UID} -G $(getent group ${DEV_GID} | cut -d: -f1) appuser; fi

# Configure PHP-FPM to run as the build-time user
RUN USER=$(getent passwd ${DEV_UID} | cut -d: -f1) \
 && GROUP=$(getent group ${DEV_GID} | cut -d: -f1) \
 && sed -i "s|^user = .*|user = $USER|" /usr/local/etc/php-fpm.d/www.conf \
 && sed -i "s|^group = .*|group = $GROUP|" /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
