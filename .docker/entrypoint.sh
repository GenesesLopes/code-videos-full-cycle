#!/bin/bash

## Frontend
cd /var/www/frontend && npm install && cd .. 

## Backend
cd backend
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

if [ ! -f ".env.testing" ]; then
    cp .env.testing.example .env.testing
fi
chown -R www-data:www-data .
composer install
php artisan key:generate
php artisan migrate

php-fpm
