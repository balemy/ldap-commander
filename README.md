[![build](https://github.com/balemy/ldap-commander/actions/workflows/build.yml/badge.svg)](https://github.com/balemy/ldap-commander/actions/workflows/build.yml)
[![static analysis](https://github.com/balemy/ldap-commander/actions/workflows/static.yml/badge.svg)](https://github.com/balemy/ldap-commander/actions/workflows/static.yml)
[![Powered by Yii 3 Framework](https://img.shields.io/badge/Powered_by-Yii_3_Framework-green.svg?style=flat)](https://www.yiiframework.com/)

<p align="center">
    <h1 align="center">LDAP Commander <sup></sup></h1>
    <h3 align="center">Demo: https://example-ldap.balemy.com/</h3>
    <h5 align="center">Username: &lt;leave empty&gt; &middot; Password: secret</h5>
    <br>
</p>

## About

LDAP Commander is a web interface for managing LDAP servers. Currently only OpenLDAP is supported.

### Features

- Browse LDAP Structure
- Create, Edit and Delete LDAP Entities
- Automatic Schema Detection
- Schema Viewer
- Server Info
- Entity Editor
  - Multi Value Support
  - Binary Attributes
  - SHA Password Hashing

## Quickstart with Docker

**Without LDAP Server:**

```
wget -O config.php https://raw.githubusercontent.com/balemy/ldap-commander/main/config/ldap.example.php

# On Port 80/443 with enabled Caddy und LetsEncrypt certs
docker run -d -p 80:80 -p 443:443 \
    --restart always \
    -e ENABLE_CADDY=true \
    -e HOSTNAME=example.com \
    -v ./config.php:/app/config/ldap.php \
    -v ./data/caddy:/data \
     balemy/ldap-commander

# On Port 8080
`docker run -it --net='host'` -p 8080:8080 -v ./config.php:/app/config/ldap.php balemy/ldap-commander

````

**Docker Compose (with bundled LDAP Server and Example data):**

```
mkdir /opt/ldap-commander
cd /opt/ldap-commander

wget -O config.php https://raw.githubusercontent.com/balemy/ldap-commander/main/docker/config.php
wget -O docker-compose.yml https://raw.githubusercontent.com/balemy/ldap-commander/main/docker/docker-compose.yml
wget -O ldap-memberof.ldif https://raw.githubusercontent.com/balemy/ldap-commander/main/resources/ldap/bitnami-openldap-memberof.ldif
wget -O ldap-example-data.ldif https://raw.githubusercontent.com/balemy/ldap-commander/main/resources/ldap/example.ldif

mkdir openldap_data
chmod 777 openldap_data

docker-compose up
```

Then open the following URL in your browser: [http://localhost:8080](http://localhost:8080)

## Quickstart with PHP & Composer

### Requirements

- PHP 8.1 with LDAP extension
- Composer

### Installation

``` 
git clone https://github.com/balemy/ldap-commander.git /opt/ldap-commander
cd /opt/ldap-commander
composer install
vi .env
``` 

### Startup

``` 
composer serve
``` 

Then open the following URL in your browser: [http://localhost:8080](http://localhost:8080)

## License

Please see [`LICENSE`](./LICENSE.md) for more information.
