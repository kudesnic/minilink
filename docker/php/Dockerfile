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

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'c31c1e292ad7be5f49291169c0ac8f683499edddcfd4e42232982d0fd193004208a58ff6f353fde0012d35fdd72bc394') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"\
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

WORKDIR /usr/src/backend


RUN PATH=$PATH:/usr/src/apps/vendor/bin:bin

