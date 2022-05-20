<p align="center">
    <h1 align="center">LDAP Commander <sup>PreBeta</sup></h1>
    <h3 align="center">Work in progress</h3>
    <br>
</p>

## About

LDAP Commander is a web interface for managing LDAP servers. Currently only OpenLDAP is supported.

## Quickstart with Docker

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
git clone https://github.com/balemy/ldap-admin.git /opt/ldap-commander
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

