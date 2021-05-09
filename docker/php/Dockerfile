FROM php:7.4-fpm

# PHP extensions
RUN apt-get update && apt-get install -y \
        nano \
        libwebp-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libxpm-dev \
        libfreetype6-dev \
        libicu-dev \
        libpq-dev \
        libonig-dev \
        libzip-dev \
        wget \
        git \
    && docker-php-ext-install -j$(nproc) mbstring pgsql pdo_pgsql tokenizer iconv mysqli  \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd


RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
zlib1g-dev \
libzmq3-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-enable apcu


RUN git clone git://github.com/mkoppanen/php-zmq.git \
 && cd php-zmq \
 && phpize && ./configure \
 && make \
 && make install \
 && cd .. \
 && rm -fr php-zmq
RUN docker-php-ext-enable zmq


WORKDIR /usr/src/backend


RUN PATH=$PATH:/usr/src/apps/vendor/bin:bin

