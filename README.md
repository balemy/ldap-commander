[![build](https://github.com/balemy/ldap-commander/actions/workflows/build.yml/badge.svg)](https://github.com/balemy/ldap-commander/actions/workflows/build.yml)
[![static analysis](https://github.com/balemy/ldap-commander/actions/workflows/static.yml/badge.svg)](https://github.com/balemy/ldap-commander/actions/workflows/static.yml)
[![Powered by Yii 3 Framework](https://img.shields.io/badge/Powered_by-Yii_3_Framework-green.svg?style=flat)](https://www.yiiframework.com/)

<p align="center">
    <h1 align="center">LDAP Commander <sup></sup></h1>
    <h3 align="center">Work in progress</h3>
    <br>
</p>

## About

LDAP Commander is a web interface for managing LDAP servers. Currently only OpenLDAP is supported.

### Features

- Browse LDAP Structure
- Create, Edit and Delete LDAP Entities
- Schema Detection
- Entity Editor
  - Multi Value Support
  - Binary Attributes
  - SHA Password Hashing


## Quickstart with Docker

**Docker Compose (with bundled LDAP Servern and Example data):**

```
# Download Docker Compose File
curl https://raw.githubusercontent.com/balemy/ldap-commander/main/docker-compose.yml --output docker-compose.yml
# Download Example LDAP Data (Optional)
curl https://raw.githubusercontent.com/balemy/ldap-commander/main/resources/ldap/example.ldif --output example.ldif
./docker-compose up
```
Default password: `password`

**Or Standalone without LDAP Server:**

```
docker run -it --net='host' \
-e CMDA_DSN='ldap://localhost:389' \
-e CMDA_BASE_DN='dc=example,dc=org' \
-e CMDA_ADMIN_DN='cn=admin,dc=example,dc=org' \
-e CMDA_ADMIN_PASSWORD= \
-p 8080:8080 balemy/ldap-commander
````

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
./cmda serve
``` 

Then open the following URL in your browser: [http://localhost:8080](http://localhost:8080)

## License

Please see [`LICENSE`](./LICENSE.md) for more information.


