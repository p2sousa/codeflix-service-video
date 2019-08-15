#!/bin/bash

echo "Alterando permissao da pasta storage...."
chmod -R 775 storage/

echo "Instalando dependencias php...."
composer install

echo "Instalando dependencias node...."
npm install

echo "rodando migrations...."
php artisan key:generate
php artisan migrate

php-fpm
