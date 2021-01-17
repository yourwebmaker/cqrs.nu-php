FROM php:8.0.0-alpine AS ext-amqp

ENV EXT_AMQP_VERSION=master

RUN docker-php-source extract \
    && apk -Uu add git rabbitmq-c-dev \
    && git clone --branch $EXT_AMQP_VERSION --depth 1 https://github.com/php-amqp/php-amqp.git /usr/src/php/ext/amqp \
    && cd /usr/src/php/ext/amqp && git submodule update --init \
    && docker-php-ext-install amqp

RUN ls -al /usr/local/lib/php/extensions

FROM php:8-fpm

ENV PHP_BUILD_DATE="20190902"
ENV PHP_OPCACHE_ENABLED=false
ENV SESSION_HANDLER=false
ENV SESSION_HANDLER_NAME=""
ENV SESSION_HANDLER_PATH=""
ENV XDEBUG_AUTOSTART=false
ENV XDEBUG_CONNECT_BACK=true
ENV XDEBUG_ENABLED=false
ENV XDEBUG_IDEKEY="docker"
ENV XDEBUG_VERSION=""
ENV XDEBUG_REMOTE_PORT=9000
ENV PHP_EXTENSION_WDDX=1
ENV PHP_OPENSSL=1
ENV TINI_VERSION v0.19.0

ADD https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini /tini
RUN chmod +x /tini

RUN apt-get update && apt-get install -y --no-install-recommends wget vim supervisor libfreetype6-dev libjpeg62-turbo-dev \
    libmcrypt-dev libpng-dev libssl-dev libaio1 git libcurl4-openssl-dev libxslt-dev \
    libicu-dev libc-client-dev libsqlite3-dev libedit-dev \
    sudo zlib1g zlib1g-dev libzip4 libzip-dev zip unzip librabbitmq-dev musl-dev && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) bcmath gd pdo_mysql calendar exif gettext shmop soap sockets intl pcntl xsl

RUN echo "---> Adding Redis" && \
    pecl install redis && \
    docker-php-ext-enable redis

RUN echo "---> Adding xDebug" && \
    pecl install "xdebug${XDEBUG_VERSION}"

RUN echo "---> Adding Zip" && \
    pecl install zip && \
    docker-php-ext-enable zip

RUN echo "---> Adding AMQp" && ln -s /usr/lib/x86_64-linux-musl/libc.so /lib/libc.musl-x86_64.so.1
COPY --from=ext-amqp /usr/local/etc/php/conf.d/docker-php-ext-amqp.ini /usr/local/etc/php/conf.d/docker-php-ext-amqp.ini
COPY --from=ext-amqp /usr/local/lib/php/extensions/no-debug-non-zts-20200930/amqp.so /usr/local/lib/php/extensions/no-debug-non-zts-20200930/amqp.so

RUN echo "---> Config sudoers" && \
    echo "www-data  ALL = ( ALL ) NOPASSWD: ALL" >> /etc/sudoers

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer && \
    mkdir /var/www/.composer && chown -R www-data:www-data /var/www/.composer

RUN ls -la /var/www/

COPY devops/php-fpm/entrypoint.sh /usr/bin/entrypoint
RUN chmod a+x /usr/bin/entrypoint

COPY devops/php-fpm/php.ini /usr/local/etc/php/conf.d/cqrs.nup-php.ini
COPY devops/php-fpm/log.conf /usr/local/etc/php-fpm.d/zz-log.conf

USER www-data

WORKDIR /var/www/html/

USER root

RUN mkdir -p var/log var/cache && chown -R www-data:www-data /var/www/ var/log var/cache && \
    chmod -R a+rw var/log var/cache

USER www-data:www-data

COPY --chown=www-data:www-data ./composer.json ./composer.lock ./symfony.lock /var/www/html/
RUN composer install --verbose --prefer-dist --no-interaction --no-dev --optimize-autoloader --no-suggest --no-scripts

COPY --chown=www-data:www-data ./bin /var/www/html/bin
COPY --chown=www-data:www-data ./config /var/www/html/config
COPY --chown=www-data:www-data ./public /var/www/html/public
COPY --chown=www-data:www-data ./src /var/www/html/src

RUN chmod -R a+x ./bin

RUN composer dump-autoload

CMD ["/tini", "--", "/usr/bin/entrypoint"]