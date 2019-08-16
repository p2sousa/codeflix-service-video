FROM nginx:1.17-alpine

LABEL maintainer="Pablo Sousa <pablosousa.ads@gmail.com>"

ARG UID=1000
ARG GID=1000

RUN apk update && apk add bash

RUN rm /etc/nginx/conf.d/default.conf

# install dockerizdr to controll dependencies containers
ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
  && tar -C /usr/local/bin -xzvf dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
  && rm dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz

WORKDIR /var/www
