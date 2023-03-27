#!/bin/bash

# Raccourci pour lancer le container docker

copy .env.docker .env
docker compose build
docker compose up -d
