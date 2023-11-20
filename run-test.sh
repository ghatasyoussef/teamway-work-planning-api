php artisan --env=testing migrate:refresh --seed # This will clean, migrate, then seed the db

## Run generting tests
# php artisan test # Run tests

## Run for generating coverage reports
php -d xdebug.mode=coverage vendor/phpunit/phpunit/phpunit --coverage-html ./docs/test-coverage