FROM php:8.2.0-fpm-alpine

RUN set -ex \
  && apk --no-cache add \
    libzip-dev \
    zip \
    libpng-dev \
    libxslt-dev \
    bash \
    icu-dev \
    openssl \
    acl \
    wget

RUN docker-php-ext-install pdo pdo_mysql zip xsl gd intl

RUN apk add --update linux-headers

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions xdebug-^3.2.2
RUN install-php-extensions @composer

WORKDIR /app

RUN echo 'pm.max_children = 30' >> /usr/local/etc/php-fpm.d/zz-docker.conf

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

CMD ["php-fpm"]