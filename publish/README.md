# OxygenProject

## Deployment Instructions

```
// update the code to latest master branch
git pull origin master

// create a local php alias to the correct version
alias php=/opt/cpanel/ea-php80/root/usr/bin/php

// install with composer
/opt/cpanel/ea-php80/root/usr/bin/php /opt/cpanel/composer/bin/composer install --no-dev
```

## Deployment Server Initial Setup Instructions

```
// copy the .env file
cp .env.example .env

// generate app key
php artisan key:generate

// edit the .env file with the server settings

// link the storage folder
php artisan storage:link

// install dusk (if you're going to run Browser Tests)
composer require --dev laravel/dusk
php artisan dusk:install
```

### Development Instructions

Migrate and seed the database
```
php artisan db:refresh
```

Run the local development watcher
```
npm run dev
```

Generate API documentation
```
// to auto generate docs and API tests. 
// WARNING: This will overwrite existing API Tests
php artisan generate:docs-tests

// to only generate the docs
php artisan generate:docs
```

Run PHPUnit Tests
```
./vendor/bin/phpunit
```

Run Dusk Tests
```
php artisan dusk --stop-on-error --stop-on-failure
```

Before releasing to production, compile the assets
```
npm run build
```

## Licence

Project Licenced to OxygenProject. [Copyright Elegant Media](https://www.elegantmedia.com.au)

## Copyright

Copyright (c) Elegant Media.
