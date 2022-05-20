# Development Documentation


## Testing

The template comes with ready to use [Codeception](https://codeception.com/) configuration.
In order to execute tests run:

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


## Docker

```
docker build -t commander1 . 
docker run -it --net='host' \
    -e CMDA_DSN='ldap://localhost:1389' \
    -e CMDA_BASE_DN='dc=example,dc=org' \
    -e CMDA_ADMIN_DN='cn=admin,dc=example,dc=org' \
    -e CMDA_ADMIN_PASSWORD= \
    -p 8080:8080 commander1 
```


