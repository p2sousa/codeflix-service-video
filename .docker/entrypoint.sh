#!/bin/bash

echo "Copiando .env da aplicacao..."
dockerize -template ./.docker/php/.env:.env

echo "Copiando .env do ambiente de teste..."
dockerize -template ./.docker/php/.env.testing:.env.testing

echo "Aguardando conexao com o banco de dados..."
dockerize -wait tcp://db:3306 -timeout 60s

echo "Alterando permissao da pasta storage..."
chmod -R 775 storage/

echo "Instalando dependencias php...."
composer install

echo "Instalando dependencias node...."
npm install

echo "rodando migrations...."
php artisan key:generate
php artisan migrate:refresh --seed

php-fpm
