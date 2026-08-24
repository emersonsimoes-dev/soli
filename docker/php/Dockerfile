FROM php:8.4-fpm-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        libpq-dev \
        libzip-dev \
        libicu-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        intl \
        gd \
        bcmath \
        pcntl \
        opcache \
        exif \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/icvb.ini
COPY entrypoint.sh /usr/local/bin/icvb-entrypoint

RUN chmod +x /usr/local/bin/icvb-entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["icvb-entrypoint"]
CMD ["php-fpm"]
