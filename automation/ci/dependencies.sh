#!/bin/bash

set -xe

# Mise à jour des paquets du serveur
apk update
apk upgrade

# Installation des dépendances
apk add --no-progress --no-cache --upgrade zip unzip git curl

# Installation des pilotes PECL
apk --no-cache add --virtual .build-deps \
g++ \
autoconf \
make && \

# Installation xdebug pour permettre la génération des rapports de couverture des tests
pecl install xdebug-2.9.2 && \
docker-php-ext-enable xdebug && \

apk del .build-deps && \
rm -r /tmp/pear/* && \
echo -e "xdebug.remote_enable=1\n\
        xdebug.remote_autostart=1\n\
        xdebug.remote_connect_back=0\n\
        xdebug.remote_port=9001\n\
        xdebug.remote_log=/var/www/html/xdebug.log\n\
        xdebug.remote_host=host.docker.internal" >>/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini &&\

# Installation des dépendances PHP supplémentaires
docker-php-ext-install bcmath sockets pcntl fileinfo

# Installation de composer
curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
