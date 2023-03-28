# Image de base
FROM php:8.2-fpm-alpine

# Définition du répertoire de travail
WORKDIR /app

# Copie de l'ensemble des fichiers
COPY . /app

# Installation des dépendances
COPY automation/ci/dependencies.sh dependencies.sh
RUN chmod +x dependencies.sh
RUN  apk add --no-cache --upgrade bash

RUN bash dependencies.sh

# Installation des vendors et des migrations DB
RUN composer install
COPY .env.docker .env
RUN php artisan key:generate
RUN php artisan migrate --force --seed

# Lancement du serveur de démo
CMD php artisan serve --host=0.0.0.0 --port=8000
EXPOSE 8000
