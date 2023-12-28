#!/bin/bash

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

docker stop selenium-test
docker rm selenium-test
docker run --detach --net=host --shm-size="2g" --name selenium-test selenium/standalone-chrome:4.8.0-20230131

docker stop openldap-test
docker rm openldap-test
docker run --detach --rm --name openldap-test \
    --net=host \
    --env BITNAMI_DEBUG=true \
    --env LDAP_CONFIG_ADMIN_ENABLED=yes \
    --env LDAP_ADMIN_USERNAME=admin \
    --env LDAP_ADMIN_PASSWORD=secret \
    --env LDAP_EXTRA_SCHEMAS=cosine,inetorgperson,nis,bitnami-openldap-memberof \
    -v ${SCRIPT_DIR}/../resources/ldap/example.ldif:/ldifs/example-quickstart.ldif \
    -v ${SCRIPT_DIR}/../resources/ldap/bitnami-openldap-memberof.ldif:/opt/bitnami/openldap/etc/schema/bitnami-openldap-memberof.ldif \
    -p 1389:1389 \
    bitnami/openldap:latest
