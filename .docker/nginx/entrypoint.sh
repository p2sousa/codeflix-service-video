#!/bin/bash

dockerize -template ./.docker/nginx/nginx.tmpl:/etc/nginx/conf.d/nginx.conf
dockerize -wait tcp://app:9000

nginx -g "daemon off;"
