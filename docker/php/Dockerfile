# Dockerfile em docker/php/Dockerfile
FROM php:8.1-fpm

# Instala dependências de sistema
RUN apt-get update \
  && apt-get install -y \
     git \
     libicu-dev \
     libonig-dev \
     libzip-dev \
     zip \
     unzip \
     libxml2-dev \
  && rm -rf /var/lib/apt/lists/*

# Instala extensões PHP necessárias
RUN docker-php-ext-install \
     intl \
     mbstring \
     pdo \
     pdo_mysql \
     zip \
     xml \
     opcache

# Instala Xdebug e configura coverage automaticamente
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && { \
       echo 'zend_extension=xdebug.so'; \
       echo 'xdebug.mode=develop,debug,coverage'; \
       echo 'xdebug.start_with_request=yes'; \
     } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Composer (multi‑stage build oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
