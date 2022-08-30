# Specify the parent image from which we build
FROM php:8.1.0-cli
WORKDIR /app
COPY . .

RUN apt-get update

#--------------------------------------------------------------------------
# Install PHP Extensions
#--------------------------------------------------------------------------
RUN \
    apt-get install libldap2-dev libicu-dev -y && \
    docker-php-ext-configure ldap intl pdo_sqlite && \
    docker-php-ext-install ldap intl pdo_sqlite && \
    php -m

#--------------------------------------------------------------------------
# Download Composer and install
#--------------------------------------------------------------------------
RUN apt-get install git unzip -y
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install

#--------------------------------------------------------------------------
# Cleanup
#--------------------------------------------------------------------------
RUN rm -rf /var/lib/apt/lists/*

#--------------------------------------------------------------------------
# Run
#--------------------------------------------------------------------------
CMD ["php", "/app/cmda", "serve", "0.0.0.0"]
EXPOSE 8080
