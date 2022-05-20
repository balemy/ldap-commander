# Specify the parent image from which we build
FROM php:8.1.0-cli
WORKDIR /app
COPY . .

#--------------------------------------------------------------------------
# Install PHP LDAP Extension
#--------------------------------------------------------------------------
RUN \
    apt-get update && \
    apt-get install libldap2-dev git unzip -y && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-install ldap

RUN php -m

#--------------------------------------------------------------------------
# Download Composer and install
#--------------------------------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install

#--------------------------------------------------------------------------
# Run
#--------------------------------------------------------------------------
CMD ["php", "/app/cmda", "serve", "0.0.0.0"]
EXPOSE 8080
