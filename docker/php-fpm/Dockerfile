# See https://github.com/docker-library/php/blob/4677ca134fe48d20c820a19becb99198824d78e3/7.0/fpm/Dockerfile
FROM php:7.4.2-fpm
ARG TIMEZONE

MAINTAINER Maxence POUTORD <maxence.poutord@gmail.com>

# change user and groups ids
RUN usermod --uid=1000 www-data && \
    groupmod --gid=1000 www-data && \
    # fix permissions
    chown -R www-data:www-data /var/www/

RUN apt-get update && apt-get install -y \
    apt-transport-https \
    procps \
    acl \
    openssl \
    acl \
    wget \
    git \
    unzip \
    gnupg

# Install xdebug
RUN yes | pecl install xdebug-2.9.8 \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
#    For non-linux users (turn off remote_connect_back, specify remote_host)
    && echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && docker-php-ext-enable xdebug

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Install yarn, Node.js
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get update && apt-get install -y \
    yarn \
    nodejs

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql opcache exif

# Install GD
RUN apt-get install -y \
          libfreetype6-dev \
          libjpeg62-turbo-dev \
          libpng-dev \
     && docker-php-ext-configure gd \
          --with-freetype \
          --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-enable gd

COPY php.ini /usr/local/etc/php/

RUN set -xe && \
  groupadd -g 1000 -o -f user && \
  useradd --shell /bin/bash -u 1000 -g user -o -c "" -m user

WORKDIR /var/www/symfony

USER www-data

RUN echo 'alias sf="php bin/console"' >> ~/.bashrc
