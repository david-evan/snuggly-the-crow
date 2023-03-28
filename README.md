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

## Table of contents

- [Quick-start](#quick-start)
    * [Requirement :](#requirement--)
    * [Démarrage de l'application](#d-marrage-de-l-application)
        + [Local](#local)
        + [Docker](#docker)
    * [Lancement des tests unitaires](#lancement-des-tests-unitaires)
- [Configuration avancée](#configuration-avanc-e)
    * [Fichiers de configuration](#fichiers-de-configuration)
    * [Version API](#version-api)
    * [Base de données](#base-de-donn-es)
- [Utilisation de l'application](#utilisation-de-l-application)
    * [Documentation](#documentation)
    * [Authentification](#authentification)
    * [Utilisateur par défaut](#utilisateur-par-d-faut)
    * [API problems](#api-problems)
    * [Logs](#logs)
- [Code Architecture](#code-architecture)
    * [Principes de conception](#principes-de-conception)
- [Déploiement en production](#d-ploiement-en-production)
- [Todo](#todo)
- [Un peu de lecture ...](#un-peu-de-lecture-)

## Quick-start

### Requirement :

- Docker (à jour)
  ou
- PHP 8.2 (CLI)
- Composer (À jour -> v2.5.5 OK)

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

#### Step by step

L'application fonctionne comme n'importe quelle application Laravel habituelle. Il est possible de réaliser
l'enchainement des commandes suivantes pour démarrer l'application sans passer par les scripts automatisés :

```shell
# Installation des vendors
composer install
# Création de la clef laravel
php artisan key:generate
# Création de la base de données + migration + seed data
php artisan migrate --seed
# Lancement du serveur web (le port peut être modifié). Peut être ignoré si lancé dans un ngnix ou un apache
php artisan serve --port=7010
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

### API problems

L'application utilise des modèles d'erreur conforme à la [RFC7807](https://www.rfc-editor.org/rfc/rfc7807). Une
documentation spéciale est disponible (`docs/api-problems.md`) pour détailler le fonctionnement de l'implémentation.

### Logs

Si nécessaire, les logs de l'application peuvent être trouvés dans le dossier : `/storage/logs`.
> Si nécessaire, attribuer des droits 755 sur le dossier `/storage`.

## Code Architecture

### Principes de conception

Une conception "DDD" avec Laravel peut vite s'avérer difficile à cause de la forte relation entre les Models éloquents
et les Entities au sens DDD.
Il n'a pas été retenu d'implémenter la chaine complète (Repositories - DTO etc...) afin de ne pas alourdir inutilement
le code.

Je cite ici des règles sur lesquelles je me suis appuyé pour la conception :

> **Rule A: Keep focus on the domain of the application.**<br> Without this we lose the very essence of DDD. Our model
> objects need to be inline with our domain and need to follow the Ubiquitous Language of the project.
> <br><br>**Rule B: Stay true to the framework.** <br>Fighting a framework is exhausting, not scalable and pointless. We
> want it to be easy to upgrade to the next versions. We want not only to have access to the entire framework's goodness
> but also to discretely leverage its power inside our domain.
> <br><br>**Rule C: Keep it simple.** <br>It is somewhat linked to the previous rule but we do not want to create and
> inject dozens of classes when User::find(1) or config('app.name') suffice. Additionally, we do not want to maintain
> two
> versions of our model objects: one that our domain understands and one that Laravel understands.

*Source: https://lorisleiva.com/conciliating-laravel-and-ddd*

Aussi, il est possible d'identifier deux grands ensemble dans l'application :

- `/app` : Qui s'occupe uniquement de récupérer les requêtes / données associées, de lancer les actions métiers associés
  et de transmettre les réponses.<br><br>
- `/domain` : Qui contient l'intégralité de la logique métier et aucune dépendance vers l'application (cette couche est
  agnostic de la couche applicative). De par la spécificité d'Eloquent, la partie Data est incluse dans cette couche.
  <br><br>
  Un soin a toutefois été apportée pour à ne pas introduire de couplage fort entre la couche Domain et les Data . Il est
  facilement envisageable de l'extraire, cependant cette extraction nécessite d'introduire plusieurs objets
  intermédiaires (Converters, DTO, Repositories) et ne semble pas justifée pour une application de cette taille, au
  risque d'entraver son évolutivité en la rendant inutilement complexe.

## Déploiement en production

**Théoriquement**, l'application pourrait être actuellement déployée en production par simple modification de quelques
valeurs d'environnement et en jouant quelques commandes laravel (celles incluses dans les scripts d'installation, avec
quelques modifications, notamment pour éviter de charger les seeders).

En pratique, plusieurs points devraient être améliorés afin d'améliorer la sécurité ou les performances de l'appli.

## Todo

Au-delà de l'aspect fonctionnel, la liste ci-après contient plusieurs points d'amélioration (non ordonnés) que j'ai noté
au cours du
développement :

- Renommer la route des articles "trashed" pour la rendre plus explicite.
- Authentification : Améliorer en utilisant plutôt un service d'authentification tiers (ex : Auth0, KeyCloack ou autre
  IdP compatible OAuth / OIDC).
- Amélioration du système pour exploitation de Laravel sanctum ou du système d'authentification.
- Ajouter une gestion de droits (via modification des `Requests`).
- Ajouter une gestion du nombre de tentatives de connexion avec alerting.
- Ajout des pipelines CI pour lancer les TA / l'analyse statique automatiquement en fonction du workflow.
- Ajouter de la recherche sur les dates (voir documentation API format).
- Filter les articles en fonction de l'auteur.
- Améliorer l'approche DDD :
    - valueObject pour l'API Key .
    - Utilisation des Repository (à débattre).
    - Délier le "Model eloquent" de l'entité business (via DTO ?).
- Améliorer performance avec du cache redis / memcache.
- Refactoriser les tests d'intégration et les améliorer pour verifier les formats d'E/S et les ajouter des tests d'échec
  pour valider les formats d'erreur.
- Améliorer la documentation swagger. On n'en fait jamais trop ! Ajouter des exemples plus parlants.
- Refactoriser la partie docker et le docker compose pour ajouter le support d'une base de données

## Un peu de lecture ...

- https://laravel.com/docs/10.x/
- https://www.mongodb.com/compatibility/mongodb-laravel-intergration
- https://lorisleiva.com/conciliating-laravel-and-ddd-part-2
