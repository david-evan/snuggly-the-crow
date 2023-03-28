<div align="center">
<img  width="75" src="docs/icons/project-icon.png" />
<br>
<br>
<h1>Snuggly The Crow</h1>
</div>

<br>
<br>

Le PoC "Snuggly The Crow" est une application de gestion d'article de blog. Sa construction repose sur une base Laravel
10 qui a été épurée pour la rendre adaptée à la construction d'une Web et à une implémentation *simplifée* du DDD.
La couche data exploite l'ORM Eloquent et est compatible avec les principaux systèmes de stockage de données (SQL ou
NoSQL).

# Table of contents

## Quick-start

### Requirement :

- Docker (à jour)
  ou
- PHP 8.2 (CLI)
- Composer

### Démarrage de l'application

Afin de simplifier le démarrage, des raccourcis `.bat / .sh` ont été ajoutés au projet. Ils embarquent l'installation
des dépendances et la construction de la base de données. Le lancement de l'application peut être réalisé soit à partir
d'un *container*, soit en local. Une fois l'application démarrée, le endpoint `http://localhost:7010` devrait fournir
quelques détails sur l'application.

> **/!\ Le premier lancement nécessite le téléchargement de nombreux paquets, (docker et/ou composer) et peut donc
prendre
> un temps relativement long en fonction de la qualité de la connexion disponible.***

#### Local

```shell
app-install-local
php artisan serve --port=7010
```

#### Docker

```shell
app-start-docker
# Par défaut, l'application démarrera sur le port 7010. Il est possible de modifier ce comportement en modifiant le docker-compose.
```

### Lancement des tests unitaires

Les tests unitaires peuvent être joués à tout moment avec où sans génération de rapport de couverture de code. Ils sont
configurés pour utiliser une base de données séparée (`sqlite`) qui n'entre pas en conflit ou n'influence pas sur le
fonctionnement ou les données de l'application.

```shell
# Lancement des tests unitaire "basics"
php artisan test
# ou 
php artisan test --parallel

# ------
# Tests unitaire avec rapport de couverture en CLI 
php artisan --coverage 

# Tests unitaire avec rapport de couverture complet en html
# Le nom du dossier _coverage-report peut être différent, mais par défaut, 
# celui-ci est exclu du git et vient se placer en haut de l'arborescence.
php artisan --coverage-html _coverage-report
```
<br>

---

## Configuration avancée

### Fichiers de configuration

La configuration principale de l'application est réalisée à l'aide du fichier `.env` présent à la racine du répertoire
principal. Des fichiers de configurations adaptés au local ou au docket sont automatiquement créés à partir des fichiers
d'exemple disponibles à la racine. (voir : `.env.local` ou `.env.docker`)

### Version API

L'application embarque une clef de configuration `APP_VERSION`. Cette clef peut être modifiée pour préfixer l'ensemble
des endpoints (à l'exception du principal) par un tag de version, par exemple : `v1`. Il est facilement envisageable de
faire ainsi tourner plusieurs versions de l'API pour garantir la rétrocompatibilité.

### Base de données

Afin de simplifier les développements et d'éviter d'imposer des dépendances fortes pour les développeurs, en local,
l'application exploite une base de données SQLite, qui ne devrait, cela va de soi, ne pas être utilisé en production.
La configuration docker s'appuie quant à elle sur une base PostgreSQL.

Il n'existe actuellement aucune dépendance au système de stockage. Il est possible de modifier la configuration pour
exploiter n'importe quel système de stockage de données persistant, y compris du noSQL (Dans ce cas, l'ORM Eloquent doit
être légèrement modifiée).

<br>

--- 

## Utilisation de l'application

### Documentation

La documentation swagger de l'API est disponible et livrée dans le dossier `/docs/swagger.yml` (
Voir : https://editor.swagger.io/).
Par ailleurs, un export de la librairie Postman (et des configurations d'environnement) est disponible dans le
dossier : `docs/postman`

### Authentification

Afin d'exploiter les API, il est nécessaire de s'authentifier à l'aide du endpoint `/auth/login` et de transmettre la
clef API délivrée dans un header `X-Api-Key` (sauf pour le endpoint d'authentification, forcement).

> Les clefs d'API délivrées par l'application expirent au bout de 2h. Par ailleurs, la génération d'une nouvelle clef
> entraîne la suppression de la précédente.
> La configuration Postman inclut une variable d'environnement qui peut être utilisée pour passer automatiquement la
> clef API à l'ensemble des requêtes.

### Utilisateur par défaut

Actuellement, il n'existe pas de système de contournement de l'authentification. Aussi, pour les besoins de la démo, un
utilisateur par défaut existe et est automatiquement créé à l'aide des seeders de base de données lors de sa
construction avec le login `snuggly` et le mot de passe `password` (très sécurisé tout ça!). Il va de soi que ce
comportement devrait être modifié en production.

## Déploiement en production

**Théoriquement**, l'application pourrait être actuellement déployée en production par simple modification de quelques
valeurs d'environnement et en jouant quelques commandes laravel (celles incluses dans les scripts d'installation, avec
quelques modifications, notamment pour éviter de charger les seeders).

En pratique, plusieurs points devraient être améliorés afin d'améliorer la sécurité ou les performances de l'appli.

## Todo

Au-delà de l'aspect fonctionnel, la liste ci-après contient plusieurs points d'amélioration (non ordonnés) que j'ai noté au cours du
développement : 


- Renommer la route des articles "trashed" pour la rendre plus explicite.
- Authentification : Améliorer en utilisant plutôt un service d'authentification tiers (ex : Auth0, KeyCloack ou autre IdP compatible OAuth / OIDC).
- Amélioration du système pour exploitation de Laravel sanctum ou du système d'authentification.
- Ajouter une gestion de droits (via modification des `Requests`).
- Ajouter une gestion du nombre de tentatives de connexion avec alerting.
- Ajout des pipelines CI pour lancer les TA automatiquement en fonction du workflow.
- Ajouter de la recherche sur les dates (voir documentation API format).
- Filter les articles en fonction de l'auteur.
- Améliorer l'approche DDD : 
  - valueObject pour l'API Key .
  - Utilisation des Repository (à débattre).
  - Délier le "Model eloquent" de l'entité business (via DTO ?).
- Améliorer performance avec du cache redis / memcache.
- Refactoriser les tests d'intégration et les améliorer pour verifier les formats d'E/S et les ajouter des tests d'échec pour valider les formats d'erreur.


## Un peu de lecture ...

- https://laravel.com/docs/10.x/
- https://www.mongodb.com/compatibility/mongodb-laravel-intergration
- https://lorisleiva.com/conciliating-laravel-and-ddd-part-2
 
