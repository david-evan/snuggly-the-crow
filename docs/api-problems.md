<div align="center">
<img  width="75" src="icons/api-problems-icon.png" />
<br>
<br>
<h1>Laravel API Problems Implementation</h1>
</div>

Ce document fourni l'ensemble des pré-requis pour ajouter le format "[API Problem](https://tools.ietf.org/html/rfc7807)"
à une application de type Laravel.
La bibliothèque inclut un nouveau ExceptionHandler qui doit être utilisé pour permettre au framework de traiter
correctement les exceptions de type API Problem.
> Pour ce test technique, implementation a été réalisée en "local" au lieu d'utiliser un package. Cette approche ne
> serait pas justifiée dans une volonté d'industrialisation sur des projets d'entreprise.

<br>

- [Usage](#usage)
    * [Modification du Handler d'exceptions](#modification-du-handler-d-exceptions)
    * [Création d'une Exception de type API Problem](#cr-ation-d-une-exception-de-type-api-problem)
    * [Configuration de l'exception](#configuration-de-l-exception)
    * [Conversion d'une Exception "classique" en Exception API Problem](#conversion-d-une-exception--classique--en-exception-api-problem)
    * [Ajout de champs personnalisés à l'API Problem](#ajout-de-champs-personnalis-s---l-api-problem)
    * [Gestion des erreurs du validator](#gestion-des-erreurs-du-validator)
- [Liste des API Problems et des converters pré-configurés](#liste-des-api-problems-et-des-converters-pr--configur-s)
    * [API Problems existants](#api-problems-existants)
    * [Converters configurés](#converters-configur-s)
- [Tests des API Problems](#tests-des-api-problems)
    * [Usage](#usage-1)

## Usage

### Modification du Handler d'exceptions

Comme indiqué en introduction, il est nécessaire de modifier le `Handler` implémenté par défaut par le framework, pour
lui demander d'utiliser celui de la bibliothèque à la place. Le chemin de ce dernier est :  `app\Exceptions\Handler.php`

Par défaut, il hérite de l'`ExceptionHandler` de Laravel. Ce dernier doit être modifié pour hériter à la place
du `ApiProblemsExceptionHandler`.

```php
namespace App\Exceptions;

use App\Library\APIProblem\Handlers\APIProblemsExceptionHandler;

class Handler extends APIProblemsExceptionHandler
{
    // ...
}

```

> **Nécessité du Accept : application-json** : <br>
> Cette bibliothèque est prévue pour apporter les fonctionnalités d'API Problem à toutes requêtes API demandant une
> sortie d'erreur au format JSON. Afin d'obtenir une sortie d'erreur correctement formatée, il est nécessaire de
> spécifier
> le header `Accept : application/json` ou, au besoin, de surcharger les requêtes Laravel pour forcer les demandes en
> JSON.

La bibliothèque "API Problem" permet de faciliter la création / la configuration d'exception qui renverront
automatiquement une réponse formatée conformément au format décrit dans la RFC 7807.

L'usage est donc le suivant :

- Le développeur identifie une erreur fonctionnelle pouvant survenir dans le code (ex : Impossible de créer un compte
  utilisateur si un compte avec un identifiant identique existe).
- Le développeur génère et configure une exception de type API Problem.
- Le code lance l'exception lorsque l'erreur se présente.
- Le handler s'occupera de formater / renvoyer la réponse au navigateur du client.

### Création d'une Exception de type API Problem

Une `Exception` de type API Problem se présente sous la même forme qu'une `Exception` classique à la différence qu'elle
hérite de la classe `App\Library\APIProblem\Exceptions\Problems\APIProblemsException`.

Dans sa forme la plus basique, elle est seulement composée d'une déclaration de classe. (Par convention, nous les
placerons dans le namespace `App\Exceptions\Problems`).

```php
namespace App\Exceptions\Problems;

use App\Library\APIProblem\Exceptions\Problems\APIProblemsException;

class MethodNotAllowedException extends APIProblemsException
{
    //...
}

```

Il est toutefois possible de lui fournir des éléments en constructeurs afin de permettre d'ajouter du `detail` à l'API
Problem ([Voir section 3 de la RFC 7807](https://tools.ietf.org/html/rfc7807#section-3)).

L'exemple ci-dessous montre une `Exception` qui accepte un paramètre d'entrée et utilise la méthode `setDetail(...)`
pour spécifier un `detail` à l'API Problem (dans l'exemple, le nom d'utilisateur déjà existant).

```php
namespace App\Exceptions\Problems;

use App\Library\APIProblem\Exceptions\Problems\APIProblemsException;

class UserAlreadyExistException extends APIProblemException
{

    /**
     * UserAlreadyExistException constructor.
     * @param string|null $username
     */
    public function __construct(?string $username)
    {
        parent::__construct();
        $username ??= '(null)';
        $this->setDetail('The user cannot be created or updated because username `'.$username.'` already exist.');
    }
}
```

### Configuration de l'exception

Une fois la classe d'`Exception` créée, il est nécessaire de la configurer pour qu'elle renvoie toujours les
informations suivantes (requises par la spécification) :

- Un type unique (`type`)
- Un titre (`title`)
- Un code HTTP spécifique (`status`)

L'ensemble de ces configurations sont disponibles au sein du fichier `config\api-problems.php` qui a été publiée dans
l'application durant la phase d'installation du package.

La configuration est simple et reprend toujours la même structure. Il suffit d'ajouter une entrée au champ `problems` du
fichier de config :

```php
[
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
    | // ....
    */
    'problems' => [
    // ...
    UserAlreadyExistException::class => [ // Nom de la classe représentant l'API Problem
        "type" => "user-already-exist",       // Nom UNIQUE de l'API Problem
        "title" => "Already existing user.",  // Titre human-readable de l'API Problem
        "status" => HttpCode::HTTP_UNPROCESSABLE_ENTITY, // Code HTTP retourné par l'API Problem
    ],
    // ...
];
```

Une fois cette étape de configuration rapide terminé, l'API problem peut être naturellement utilisé dans n'importe
quelle section du code.
> L'exemple ci-dessous est volontairement simplifiée et ne tient pas compte d'une structure DDD.

L'exemple ci-dessous montre l'utilisation de l'API problem que nous venons de créer :

```php

class UserController extends APIController
{

    //...

    /**
     * @POST : api/users/
     * Créé une ressource
     * @param StoreUser $request
     * @return \Illuminate\Http\Response
     * @throws UserAlreadyExistException
     */
    public function store(StoreUser $request)
    {
        $username = $request->input('username');

        if (!empty(User::findByUsername($username)->first()))
            throw new UserAlreadyExistException($username); // Déclenche la réponse API Problem.

        // ...
    }
    // ...
}
```

### Conversion d'une Exception "classique" en Exception API Problem

Dans certain cas, il peut être nécessaire de convertir une `Exception` "classique" en API Problem. Ce cas se présente
par exemple lorsque l'on souhaite que les `Exception` déclenchée par le framework soient formatées sous le format API
Problem.

Le fichier de configuration `config\api-problems.php` dispose d'un champ `converter` qui fonctionne sous le
format : `Exception à convertir => Exception API Problem à utiliser`.

Voici un aperçu des valeurs déjà existantes:

```php
[
    // ...
    'converter' => [
        ModelNotFoundException::class => ResourceNotFoundException::class,
        NotFoundHttpException::class => RouteNotFoundException::class,
        MethodNotAllowedHttpException::class => MethodNotAllowedException::class,
        AuthenticationException::class => UnauthenticatedUserException::class
    ]
];

```

Pour bien illustrer ce fonctionnement, prenons l'exemple de la conversion du `ModelNotFoundException` qui est déclenchée
de la récupération d'un model qui n'existe pas.

Par exemple, le code suivant lancerait une `Exception` de type `ModelNotFoundException` :

```php
class UserController extends APIController
{
    /**
     * @GET : api/users/{user}
     * Affiche la liste des ressources
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user) // Si user n'existe pas, une exception de type ModelNotFoundException sera lancée.
    {
        return $user;
    }
}
```

Le converter permet donc de convertir l'erreur afin d'obtenir un retour API conforme à la spécification API Problem.

Ainsi, pour l'exemple ci-dessus, le code de l'`Exception` serait :

```php

class ResourceNotFoundException extends APIProblemException
{
    /**
     * ResourceNotFoundException constructor.
     * @param ModelNotFoundException $modelNotFoundException
     * @throws \ReflectionException
     */
    public function __construct(ModelNotFoundException $modelNotFoundException)
    {
        parent::__construct();
        $model = (new \ReflectionClass($modelNotFoundException->getModel()))->getShortName();
        $ids = implode(' | ', $modelNotFoundException->getIds());
        $this->setDetail('['.$model.'] `'.$ids.'` appear to be not existing');
    }
}
```

La configuration serait la suivante :

```php
    // ...
    ResourceNotFoundException::class => [
        "type" => "resource-not-found",
        "title" => "The resource you looking for cannot be found.",
        "status" => HttpCode::HTTP_NOT_FOUND,
    ],
```

Ainsi, chaque fois qu'une `Exception` de type `ModelNotFoundException` sera levée, l'erreur API renvoyée sera conforme à
la spécifique API Problem.

```json
{
    "title": "The resource you looking for cannot be found.",
    "type": "resource-not-found",
    "status": 404,
    "detail": "[User] `123456789` appear to be not existing"
}
```

> *NB* : L'exception décrite ci-dessous fait partie des API Problem déjà configurés dans la bibliothèque.

### Ajout de champs personnalisés à l'API Problem

La spécification API n'interdit pas d'ajout des champs supplémentaires à une réponse en plus des
champs `title - status - type`.

Il est possible, lors de la création de l'API Problem, d'utiliser la méthode `addCustomAttributes(key, value)` pour
ajouter un ou plusieurs champs personnalisés à l'API Problem.

Par exemple, ci-dessous montre comment créer un API Problem permettant d'afficher une liste de paramètres invalides dans
un champ `invalid-param`.

```php
// ...
    /**
     * RequestValidationException constructor.
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        parent::__construct();

        $invalidParams = [];
        foreach ($validator->errors()->messages() as $paramName => $failedReason)
            $invalidParams[]=['name' => $paramName, 'reasons' => $failedReason];

        $this->addCustomAttributes('invalid-params', $invalidParams);
    }
```

Après configuration de l'API Problem, le résultat ressemblera à :

```json
{
    "title": "Your request parameters didn't validate.",
    "type": "validation-error",
    "status": 400,
    "invalid-params": [
        {
            "name": "username",
            "reasons": [
                "The username field is required."
            ]
        },
        {
            "name": "email",
            "reasons": [
                "The email field is required."
            ]
        }
    ]
}

```

### Gestion des erreurs du validator

Comme montré dans la section précédente, lors qu'une requête parvient à une API il est naturel d'écrire les règles de
validation dans un objet `Request` personnalisé afin de s'assurer de la conformité des données qui parviendront
au `Controller`.

Afin de retourner les erreurs au format API Problem, il est nécessaire de surcharger la
méthode `failedValidation(Validator $validator)` de l'objet `FormRequest` pour lui indiquer de lancer une `Exception` de
type `RequestValidationException` lorsque la validation échoue.

Une classe abstraite incluant cette modification est disponible et peut servir de base à l'ensemble des requêtes. Elle
se trouve dans le namespace : `App\Library\APIProblem\Http\Requests\API\ApiRequest`.

```php

/**
 * APIRequest Base
 * Class ApiRequest
 * @package App\Http\Requests
 */
abstract class ApiRequest extends FormRequest
{
    /**
     * Comportement en cas d'échec de validation
     * @param Validator $validator
     * @throws RequestValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new RequestValidationException($validator);
    }

    /**
     * Force la requête à être traitée comme une requête JSON
     * @return bool
     */
    public function wantsJson()
    {
        return true;
    }
}

```

---

## Liste des API Problems et des converters pré-configurés

Afin de simplifier les développements futurs, ce package et le fichier configuration `config\api-problems.php`
embarquent plusieurs API Problem couramment rencontrés dans une application.
Ils sont tous situés dans le namespace : `App\Library\APIProblem\Exceptions\Problems\`.

Vous pouvez librement modifier leur configuration et/ou les surcharger pour les adapter aux besoins de chaque
application.

### API Problems existants

| API Problem | Description | Type | Title | Code HTTP |
|-|-|-|-|-|
| `RouteNotFoundException` | Sera déclenché lors d'une tentative d'accès à une route inexistante. | `api-not-found` | No
routes match this URL | 404 |
| `MethodNotAllowedException` | Sera déclenché lors d'une tentative d'accès à une route API avec une méthode non
supportée (Ex : GET au lieu de POST). | `method-not-allowed` | The HTTP method used is not allowed | 405 |
| `ResourceNotFoundException` | Sera déclenché lors d'une tentative d'accès à une ressource inexistante (Ex :
Utilisateur inconnu). | `resource-not-found` | The resource you looking for cannot be found | 404 |
| `RequestValidationException` | Peut être déclenchée lorsque les règles de validation requises par une API n'ont pas
été respectées. | `validation-error` | Your request parameters didn't validate | 400 |
| `UnauthenticatedUserException` | Sera déclenché lors d'une tentative d'accès à une ressource nécessitant une
authentification qui n'a pas pu être validée ou qui n'est pas présente. | `authentication-error` | End user cannot be
found | 401 |
| `InvalidAPIKeysException` | Peut être déclenchée lorsqu'une clef API invalide est envoyé à l'
application. | `invalid-api-key` | Unauthorized access | 401 |

### Converters configurés

Certain converters sont par ailleurs déjà configurés.

| Exception | API Problem |
|-|-|
| `Illuminate\Database\Eloquent\ModelNotFoundException` | `ResourceNotFoundException` |
| `Symfony\Component\HttpKernel\Exception\NotFoundHttpException` | `RouteNotFoundException` |
| `Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException` | `MethodNotAllowedException` |
| `Illuminate\Auth\AuthenticationException` | `UnauthenticatedUserException` |

## Tests des API Problems

Afin de faciliter les tests fonctionnels des réponses au format API Problems, il est possible d'utiliser / d'étendre
un `trait` contenu dans cette bibliothèque. Ce dernier contient une méthode permettant de tester facilement le contenu
d'une réponse au format API Problem. Il contient aussi les tests des différents problems inclus dans cette bibliothèque.

### Usage

Son utilisation est très simple, il suffit d'inclure le `trait` dans n'importe qu'elle classe de test et d'utiliser les
méthodes fournies.

```php
use App\Library\APIProblem\Tools\ApiProblemsResponseTester;

class ExampleTestCLass extends TestCase
{
    use ApiProblemsResponseTester;
    public function test_failModelBinding()
    {
        $response = $this->get(route('model.show', ['model' => 'invalidModel']));
        $response->assertStatus(HttpCode::HTTP_NOT_FOUND);
        $this->testResourceNotFoundResponse($response->getContent());
    }
}
```
