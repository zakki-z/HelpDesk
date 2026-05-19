FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    git \
    icu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip

# Install and enable required PHP extensions
RUN docker-php-ext-install \
    intl \
    opcache \
    pdo \
    pdo_pgsql \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]