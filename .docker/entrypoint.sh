#!/bin/bash

## Frontend
cd /var/www/frontend && yarn && cd .. 

## Backend
cd backend
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

if [ ! -f ".env.testing" ]; then
    cp .env.testing.example .env.testing
fi

if [ ! -L "public/storage" ]; then
    cd public && ln -s /var/www/backend/storage/app/public storage
    cd ../
fi

chown -R www-data:www-data .
composer install
php artisan key:generate
php artisan migrate

php-fpm
