# Specify the parent image from which we build
FROM php:8.1-cli
WORKDIR /app
COPY . .

RUN apt-get update

#--------------------------------------------------------------------------
# Install PHP Extensions
#--------------------------------------------------------------------------
RUN \
    apt-get install libldap2-dev libicu-dev libsqlite3-0 libsqlite3-dev -y && \
    docker-php-ext-configure ldap  && \
    docker-php-ext-install ldap && \
    docker-php-ext-configure intl  && \
    docker-php-ext-install intl && \
    docker-php-ext-configure pdo_sqlite  && \
    docker-php-ext-install pdo_sqlite && \
    docker-php-ext-configure zip  && \
    docker-php-ext-install zip && \
    php -m

#--------------------------------------------------------------------------
# Download Composer and install
#--------------------------------------------------------------------------
RUN apt-get install git unzip -y
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev

#--------------------------------------------------------------------------
# Cleanup
#--------------------------------------------------------------------------
RUN rm -rf /var/lib/apt/lists/*

#--------------------------------------------------------------------------
# Run
#--------------------------------------------------------------------------
CMD ["php", "/app/cmda", "serve", "0.0.0.0"]
EXPOSE 8080
