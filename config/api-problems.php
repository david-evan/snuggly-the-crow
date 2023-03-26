<?php

use App\Library\APIProblem\Exceptions\Problems\BadRequestException;
use App\Library\APIProblem\Exceptions\Problems\InvalidAPIKeysException;
use App\Library\APIProblem\Exceptions\Problems\MethodNotAllowedException;
use App\Library\APIProblem\Exceptions\Problems\RequestValidationException;
use App\Library\APIProblem\Exceptions\Problems\ResourceNotFoundException;
use App\Library\APIProblem\Exceptions\Problems\RouteNotFoundException;
use App\Library\APIProblem\Exceptions\Problems\UnauthenticatedUserException;
use App\Library\SDK\Definitions\HttpCode;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
|--------------------------------------------------------------------------
| API Problems Configuration File (array)
|--------------------------------------------------------------------------
| Ce fichier contient l'ensemble de la configuration des API problems.
| Il est divisé en deux sections
|   - Les problems, qui décrivent le format des "API Problems" lorsque les
|     exceptions correspondantes sont lancées.
|
|   - Le converter, qui permet de transformer une exception classique de
|     en API Problem, chaque fois qu'elle sera rencontré.
|
| NB : Peu importe l'application, les API problems ne sont renvoyés seulement
|         aux requêtes API (c'est à dire échangeant des données au format JSON).
*/
return [

    /*
    |--------------------------------------------------------------------------
    | Problems (array)
    |--------------------------------------------------------------------------
    | Cette section définie les propriétés génériques correspondant à la
    | norme "API Problems" lié à chaque erreur de type "ApiProblemException".
    |
    | Le format d'un API Problem est le suivant :
    |  - (array)[APIProblemExceptionName::class]
    |         - (string)[Type] : Type unique de l'erreur.
    |         - (string)[Title] : Titre de l'erreur
    |         - (int)[Status] : Code HTTP retourné. (Doit être un code valide)
    |
    | @see : https://tools.ietf.org/html/rfc7807
    |
    |
    | NB : Les exceptions de type ApiProblemException non décrites seront
    | chargée à partir de la config "default".
    | /!\ L'attribut "default" ne devrait jamais être utilisé, il sert uniquement
    | de "back-up" afin de palier d'éventuels oublis.
    */
    'problems' => [

        /**
         * APPLICATION ERRORS
         *
         * Cette section contient les erreurs propres aux différentes applications.
         * Les nouvelles erreurs devraient être ajoutées ci-dessous.
         */

        InvalidAPIKeysException::class => [
            "type" => "invalid-api-key",
            "title" => "Unauthorized access.",
            "status" => HttpCode::HTTP_OK
        ],


        /**
         * LARAVEL FRAMEWORK ERRORS
         *
         * Cette section contient les erreurs "générique" pouvant être
         * rencontrés par toute application.
         */

        RouteNotFoundException::class => [
            "type" => "api-not-found",
            "title" => "No routes match this URL.",
            "status" => HttpCode::HTTP_NOT_FOUND,
        ],

        MethodNotAllowedException::class => [
            "type" => "method-not-allowed",
            "title" => "The HTTP method used is not allowed.",
            "status" => HttpCode::HTTP_METHOD_NOT_ALLOWED,
        ],

        ResourceNotFoundException::class => [
            "type" => "resource-not-found",
            "title" => "The resource you looking for cannot be found.",
            "status" => HttpCode::HTTP_NOT_FOUND,
        ],

        RequestValidationException::class => [
            "type" => "validation-error",
            "title" => "Your request parameters didn't validate.",
            "status" => HttpCode::HTTP_BAD_REQUEST,
        ],

        UnauthenticatedUserException::class => [
            "type" => "authentication-error",
            "title" => "End user cannot be found.",
            "status" => HttpCode::HTTP_UNAUTHORIZED,
        ],

        BadRequestException::class => [
            "type" => "bad-request",
            "title" => "Bad request",
            "status" => HttpCode::HTTP_BAD_REQUEST,
        ],



        /**
         * DEFAULT ERROR
         *
         * Représente l'erreur API problem générique. Si une erreur se présente
         * lors d'une requête API et que celle-ci n'est pas gérée correctement,
         * l'erreur ci-dessous sera renvoyée.
         */
        "default" => [
            "type" => "generic-error",
            "title" => "A error occured",
            "status" => 400,
        ]
    ],

    'converter' => [
        ModelNotFoundException::class => ResourceNotFoundException::class,
        NotFoundHttpException::class => RouteNotFoundException::class,
        MethodNotAllowedHttpException::class => MethodNotAllowedException::class,
        AuthenticationException::class => UnauthenticatedUserException::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Renderable Exception (bool)
    |--------------------------------------------------------------------------
    | Indique si une exception de type "RenderableException" doit être
    | reporté en tant qu'erreur ou non.
    */
    'report-renderable-exception' => false,
];
