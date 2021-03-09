FROM php:8-cli-buster

RUN apt update
RUN apt upgrade -y
RUN apt install -y apt-utils libzip-dev
RUN docker-php-ext-install shmop
RUN docker-php-ext-install sysvshm
RUN docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app