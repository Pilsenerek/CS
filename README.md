# Setup project

1. Prepare environment, official docker images `php:8.3-apache` and `mariadb:11` are recommended
2. App entry is `/public/index.php`
3. Clone repository 
4. Fetch dependencies `composer install`.
5. Set DB credentials in `/.env`
6. Create DB `bin/console doctrine:database:create`.
7. Create schema `bin/console doctrine:schema:create`.

# Run tests
`php bin/phpunit`

# API doc

JWT key is defined in `/.env`

File in OpenAPI standard:
`/public/doc/source.yaml`
can be used with: https://editor.swagger.io/
