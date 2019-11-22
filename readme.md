<h1 align="center">
    Microserviço de catalogo e video da Codeflix.
</h1>

<h4 align="center">
  codeflix é um clone do netflix, projeto desenvolvido para fins de estudo de microserviço.
</h4>

[![Build Status](https://travis-ci.org/p2sousa/codeflix-service-video.svg?branch=master)](https://travis-ci.org/p2sousa/codeflix-service-video)
[![codecov](https://codecov.io/gh/p2sousa/codeflix-service-video/branch/master/graph/badge.svg)](https://codecov.io/gh/p2sousa/codeflix-service-video)
<img alt="GitHub top language" src="https://img.shields.io/github/languages/top/p2sousa/codeflix-service-video.svg">
<img alt="GitHub language count" src="https://img.shields.io/github/languages/count/p2sousa/codeflix-service-video.svg">
<a href="https://github.com/p2sousa/codeflix-service-video/commits/master">
    <img alt="GitHub last commit" src="https://img.shields.io/github/last-commit/p2sousa/codeflix-service-video.svg">
</a>
<img alt="GitHub" src="https://img.shields.io/github/license/p2sousa/codeflix-service-video.svg">

## Instalaçaão usando Docker

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
