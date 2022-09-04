## Testing

The template comes with ready to use [Codeception](https://codeception.com/) configuration.
In order to execute tests run:

**Start Test Docker Server:**
 
```
docker run  --rm --name test-openldap \
  --env LDAP_CONFIG_ADMIN_ENABLED=true \
  --env LDAP_ADMIN_USERNAME=admin \
  --env LDAP_ADMIN_PASSWORD=secret \
  --detach \
  -p 1389:1389 \
  bitnami/openldap:latest
  
  -v /srv/http/luke/balemy/ldap-commander/resources/ldap/example.ldif:/ldifs/example-quickstart.ldif \
  
```

**Start Test Docker Server:**

```
docker run --net=host --shm-size="2g" selenium/standalone-chrome
```

**Start Tests:**

```
composer run serve > ./runtime/yii.log 2>&1 &
vendor/bin/codecept run
```




### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/).

To run static analysis:

```shell
./vendor/bin/psalm
```
