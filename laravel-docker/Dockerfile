FROM php:8.2-fpm

WORKDIR /work-planning
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libpq-dev \
    locales \
    zip \
    vim \
    unzip \
    git \
    zip \
    exif \
    sudo\
    curl
    
RUN apt-get update \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt-get install -y libzip-dev default-mysql-client

RUN docker-php-ext-install mysqli zip pdo_mysql
RUN docker-php-ext-enable mysqli zip pdo_mysql
RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd --gid 10001 laraveluser
RUN useradd --gid 10001 --uid 10001 --home-dir /work-planning laraveluser

COPY --chown=laraveluser:laraveluser ./ /work-planning

USER laraveluser
RUN composer install 
# RUN php artisan migrate --seed 

RUN ls -l
RUN pwd
EXPOSE 8000
# ENTRYPOINT ["tail", "-f", "/dev/null"]
# ENTRYPOINT ["php", "artisan", "serve", "--host", "0.0.0.0"]
ENTRYPOINT ["/bin/bash", "-c", "./start-laravel.sh"]

