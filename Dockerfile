FROM php:7.4.33-apache

RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list && \
    apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo_mysql intl zip bcmath\
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache-uploads-deny.conf /etc/apache2/conf-available/uploads-deny.conf
RUN a2enconf uploads-deny

WORKDIR /var/www/html

ARG INSTALL_DEV=0

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer

# Install from the lockfile. Runtime configuration (including secrets) is
# supplied by the environment; Composer scripts are deferred to runtime.
COPY composer.json composer.lock ./
RUN if [ "$INSTALL_DEV" = "1" ]; then \
        composer install --no-interaction --prefer-dist --no-progress --no-scripts; \
    else \
        composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts; \
    fi

COPY . .

RUN if [ "$INSTALL_DEV" = "1" ]; then \
        composer dump-autoload --optimize --no-scripts; \
    else \
        composer dump-autoload --no-dev --optimize --no-scripts; \
    fi \
    && mkdir -p var \
    && chown -R www-data:www-data var
