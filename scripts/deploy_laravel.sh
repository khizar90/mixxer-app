#!/bin/bash

# Enter mixxer directory
cd /var/www/mixxer/

sudo rm -r storage/logs/laravel.log

sudo npm install
sudo npm run build

# Install dependencies
export COMPOSER_ALLOW_SUPERUSER=1
sudo composer install --prefer-dist --no-dev -o --ignore-platform-reqs -d /var/www/mixxer/


# Migrate all tables
php /var/www/mixxer/artisan migrate

# Clear any previous cached views
php /var/www/mixxer/artisan cache:clear
php /var/www/mixxer/artisan view:clear

# Optimize the application
php /var/www/mixxer/artisan optimize

sudo chgrp -R www-data public storage bootstrap/cache
sudo chmod -R ug+rwx public storage bootstrap/cache
