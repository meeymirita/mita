#FROM php:8.2.29-fpm
FROM php:8.4-fpm

# Устанавливаем необходимые пакеты + supervisor
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zlib1g-dev \
    libicu-dev \
    libgmp-dev \
    libjpeg-dev \
    libwebp-dev \
    netcat-openbsd \
    redis-tools \
    supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-configure gd --with-jpeg --with-webp
RUN docker-php-ext-install zip pdo_mysql mbstring exif pcntl bcmath gd intl gmp sockets
RUN docker-php-ext-enable gd

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
