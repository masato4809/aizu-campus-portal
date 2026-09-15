# use PHP official image
FROM php:8.3.3-apache-bullseye

# Debian 11 (bullseye) is EOL; the live deb.debian.org mirror periodically
# rotates old package builds out from under bullseye/bullseye-security,
# causing apt-get install to 404 on packages that were fetchable moments
# earlier. Pin apt to a fixed snapshot so builds stay reproducible.
RUN sed -i \
    -e 's|^deb http://deb.debian.org/debian bullseye main|# &|' \
    -e 's|^# deb http://snapshot.debian.org/archive/debian/\([0-9TZ]*\) bullseye main|deb http://snapshot.debian.org/archive/debian/\1 bullseye main|' \
    -e 's|^deb http://deb.debian.org/debian-security bullseye-security main|# &|' \
    -e 's|^# deb http://snapshot.debian.org/archive/debian-security/\([0-9TZ]*\) bullseye-security main|deb http://snapshot.debian.org/archive/debian-security/\1 bullseye-security main|' \
    -e 's|^deb http://deb.debian.org/debian bullseye-updates main|# &|' \
    -e 's|^# deb http://snapshot.debian.org/archive/debian/\([0-9TZ]*\) bullseye-updates main|deb http://snapshot.debian.org/archive/debian/\1 bullseye-updates main|' \
    /etc/apt/sources.list \
 && printf 'Acquire::Check-Valid-Until "false";\n' > /etc/apt/apt.conf.d/99no-check-valid-until

# install pecl extensions
RUN apt-get update \
 && apt-get install -y libzip-dev unzip \
 && docker-php-ext-install zip pdo mysqli pdo_mysql \
 && docker-php-ext-install opcache \
 && yes '' | pecl install redis \
 && docker-php-ext-enable redis

# Decode and resize Campus club images (JPEG / PNG / WebP).
RUN apt-get update \
 && apt-get install -y libjpeg62-turbo-dev libpng-dev libwebp-dev \
 && docker-php-ext-configure gd --with-jpeg --with-webp \
 && docker-php-ext-install gd

# install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "[xdebug]" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# install composer
RUN curl https://getcomposer.org/download/2.7.1/composer.phar -o /usr/local/bin/composer -s \
 && chmod 755 /usr/local/bin/composer

# install node.js
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -\
 && apt-get install -y nodejs

# install git for local
RUN apt-get update && \
    apt-get install -y git
RUN git config --global --add safe.directory /var/www/html

# set configuration for apache and php
COPY apache-virtualhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY php.ini /usr/local/etc/php/php.ini
COPY campus-upload.ini /usr/local/etc/php/conf.d/campus-upload.ini
COPY server.sh /usr/local/bin/server.sh
RUN chmod 755 /usr/local/bin/server.sh
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

# set entrypoint
ENTRYPOINT ["/usr/local/bin/server.sh"]
