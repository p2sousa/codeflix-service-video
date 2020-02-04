#!/bin/bash

### FRONT-END
echo "Configurando Front-End da aplicação"
npm config set cache /var/www/.npm-cache --global
cd /var/www/frontend && npm install && cd ..

### BACK-END
echo "Configurando Back-End da aplicação"
echo "Copiando .env da aplicacao..."
dockerize -template ./.docker/php/.env:backend/.env
dockerize -template ./.docker/php/.env.testing:backend/.env.testing

echo "Aguardando conexao com o banco de dados..."
dockerize -wait tcp://db:3306 -timeout 60s

cd backend
chmod -R 775 storage/
composer install

php artisan key:generate
php artisan migrate:refresh --seed
php artisan storage:link

php-fpm
