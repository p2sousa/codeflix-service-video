## codeflix-service-video
[![Build Status](https://travis-ci.org/p2sousa/codeflix-service-video.svg?branch=master)](https://travis-ci.org/p2sousa/codeflix-service-video)
[![codecov](https://codecov.io/gh/p2sousa/codeflix-service-video/branch/master/graph/badge.svg)](https://codecov.io/gh/p2sousa/codeflix-service-video)

Microserviço de video da codeflix, construido com Laravel.

## Install com Docker

Clone esse repositorio e rode o `docker-compose up -d`, aguarde o docker efetuar o build e subir os containers. 

``` bash
$ git clone https://github.com/p2sousa/codeflix-service-video.git

$ cd codeflix-service-video

$ docker-compose up -d

``` 

O processo de `up` da app vai rodar automaticamente o `composer install`, `npm install` e `php artisan migrate:refresh --seed`.

Acompanhe os `logs` usando `docker-compose logs app`, `docker-compose logs db` e `docker-compose logs nginx`.

Verifique a lista de `endpoints` do microserviço com o seguinte comando:

``` bash

$ docker exec -it codeflix-video-app php artisan route:list

``` 

## Testes

Para executar os testes entre no container e rode o comando.

``` bash

$ docker exec -it codeflix-video-app vendor/bin/phpunit

``` 
