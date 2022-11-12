[![build](https://github.com/balemy/ldap-commander/actions/workflows/build.yml/badge.svg)](https://github.com/balemy/ldap-commander/actions/workflows/build.yml)
[![static analysis](https://github.com/balemy/ldap-commander/actions/workflows/static.yml/badge.svg)](https://github.com/balemy/ldap-commander/actions/workflows/static.yml)
[![Powered by Yii 3 Framework](https://img.shields.io/badge/Powered_by-Yii_3_Framework-green.svg?style=flat)](https://www.yiiframework.com/)

<p align="center">
    <h1 align="center">LDAP Commander <sup></sup></h1>
    <h3 align="center">Demo: https://example-ldap.balemy.com/</h3>
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

**Docker Compose (with bundled LDAP Servern and Example data):**

```
git clone https://github.com/balemy/ldap-commander.git /opt/ldap-commander
cd /opt/ldap-commander
./docker-compose up
```

**Or Standalone without LDAP Server:**

```
docker run -it --net='host' \
-e LDAPCOM_CONN1_TITLE='My LDAP Server' \
-e LDAPCOM_CONN1_DSN='ldap://localhost:389' \
-e LDAPCOM_CONN1_BASEDN='dc=example,dc=org' \
-e LDAPCOM_CONN1_ADMIN_DN='cn=admin,dc=example,dc=org' \
-e LDAPCOM_CONN1_ADMIN_PASSWORD= \
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
composer serve
``` 

Then open the following URL in your browser: [http://localhost:8080](http://localhost:8080)

## License

Please see [`LICENSE`](./LICENSE.md) for more information.


