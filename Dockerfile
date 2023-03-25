# Image de base
FROM php:8.2-fpm-alpine

# Définition du répertoire de travail
WORKDIR /app

# Installation des dépendences
COPY automation/ci/dependencies.sh dependencies.sh
RUN chmod +x dependencies.sh
RUN  apk add --no-cache --upgrade bash

RUN bash dependencies.sh

# Lancement du serveur de démo
CMD php artisan serve --host=0.0.0.0 --port=8000
EXPOSE 8000
