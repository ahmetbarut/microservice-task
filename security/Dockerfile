FROM php:8.3-fpm

# Çeşitli bağımlılıkların yüklenmesi
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install sockets

# Composer yüklenmesi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

RUN composer install

# Laravel uygulaması için gerekli izinler
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
