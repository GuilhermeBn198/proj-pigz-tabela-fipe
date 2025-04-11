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

# Composer (do multi‑stage build oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
