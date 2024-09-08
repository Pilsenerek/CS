# Setup project
1. Prepare environment:
   - official docker images `php:8.3-apache` and `mariadb:11` are recommended
   - see Symfony requirements: https://symfony.com/doc/current/setup.html
   - PHP bcmath extension is required: https://www.php.net/manual/en/book.bc.php
2. App entry is `/public/index.php`
3. Clone repository 
4. Fetch dependencies `composer install`.
5. Set DB credentials in `/.env`
6. Create DB `bin/console doctrine:database:create`.
7. Create schema `bin/console doctrine:schema:create`.

# Run tests
`php bin/phpunit`

# Configuration
API key & interest rate: `/.env`

# API doc
File in OpenAPI standard:
`/public/doc/source.yaml`
can be used with: https://editor.swagger.io/

**Important:** JWT token is used as API key and provided as `Authorization: "Bearer eyJhbGciOiJ..."`
