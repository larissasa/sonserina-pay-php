FROM php:7.2-fpm

WORKDIR /var/www/html

ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
	
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libicu-dev \
    zlib1g-dev \
    libpq-dev \
    libzip-dev \
    libpcre3-dev \
	libpng-dev \
	libjpeg62-turbo-dev \
	libfreetype6-dev \
	libxml2-dev \
	unzip \
    vim cron zip curl git libaio1 \
    && docker-php-ext-install mysqli pgsql pdo pdo_mysql pdo_pgsql opcache intl gd mbstring zip soap \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

# INSTALL COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
	
