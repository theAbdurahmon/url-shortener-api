FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    postgresql-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath

# Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]