# Specify the parent image from which we build
FROM php:8.2-cli
WORKDIR /app
COPY . .


RUN apt-get update

#--------------------------------------------------------------------------
# Install PHP Extensions
#--------------------------------------------------------------------------
RUN \
    apt-get install libldap2-dev libicu-dev libsqlite3-0 libsqlite3-dev libzip-dev -y && \
    docker-php-ext-configure ldap  && \
    docker-php-ext-install ldap && \
    docker-php-ext-configure intl  && \
    docker-php-ext-install intl && \
    docker-php-ext-configure pdo_sqlite  && \
    docker-php-ext-install pdo_sqlite && \
    docker-php-ext-configure zip  && \
    docker-php-ext-install zip && \
    php -m

#-----------------------------------------------------------------------------
# Install Caddy
#-----------------------------------------------------------------------------
RUN apt-get update && apt-get install -y debian-keyring debian-archive-keyring curl gnupg && \
    curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | gpg --dearmor -o /usr/share/keyrings/caddy-stable.gpg && \
    curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | tee /etc/apt/sources.list.d/caddy-stable.list && \
    apt-get update && apt-get install caddy -y


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

# Add ENV options
ENV ENABLE_CADDY=false
ENV HOSTNAME=localhost

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose both sets of ports (only used if ENABLE_CADDY=true)
EXPOSE 8080 80 443

ENTRYPOINT ["/entrypoint.sh"]
