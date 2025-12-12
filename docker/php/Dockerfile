FROM php:8.4-cli

# Системные пакеты
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/php-custom.ini

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader
