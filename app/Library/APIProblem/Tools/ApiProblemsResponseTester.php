<?php


namespace App\Library\APIProblem\Tools;

use App\Library\APIProblem\Exceptions\Problems\InvalidAPIKeysException;
use App\Library\APIProblem\Exceptions\Problems\MethodNotAllowedException;
use App\Library\APIProblem\Exceptions\Problems\RequestValidationException;
use App\Library\APIProblem\Exceptions\Problems\ResourceNotFoundException;
use App\Library\APIProblem\Exceptions\Problems\RouteNotFoundException;

/**
 * Trait ApiProblemsResponseTester
 *
 * Ce trait permet d'ajouter facilement à n'importe qu'elle classe de
 * tests héritant de PHP Unit des fonctionnalités permettant de vérifier
 * le bon format des API Problems qui seront retournées.
 *
 * Elle peut être étendue pour ajouter les erreurs de l'application
 *
 */
trait ApiProblemsResponseTester
{
    /**
     * Test générique de la structure du json "APIProblem"
     * @param string $problemClass
     * @param string $jsonResponse
     * @return array
     */
    protected function testGenericApiProblemDataStructure(string $problemClass, string $jsonResponse) : array
    {
        // Configuration du "problem"
        $problemConfig = config('api-problems.problems.'.$problemClass);

        // Préparation des données
        $responseData = json_decode($jsonResponse, true);

        $this->assertTrue(($responseData['title'] ?? null) === $problemConfig['title']);
        $this->assertTrue(($responseData['type'] ?? null) === $problemConfig['type']);
        $this->assertTrue(($responseData['status'] ?? null) === $problemConfig['status']);

        $this->assertIsInt($responseData['status'] ?? null);

        return $responseData;
    }

    /**
     * Test que la réponse fournie est bien conforme au "problem"
     * RequestValidation
     * @param string $jsonResponse
     */
    protected function testRequestValidationResponse(string $jsonResponse) : void
    {
        $testedResponse = $this->testGenericApiProblemDataStructure(RequestValidationException::class, $jsonResponse);
        $this->assertArrayHasKey('invalid-params', $testedResponse);
    }

    /**
     * Test que la réponse fournie est bien conforme au "problem"
     * InvalidAPIKey
     * @param string $jsonResponse
     */
    protected function testInvalidApiKeysResponse(string $jsonResponse) : void
    {
        $testedResponse = $this->testGenericApiProblemDataStructure(InvalidAPIKeysException::class, $jsonResponse);
        $this->assertArrayHasKey('detail', $testedResponse);
    }

    /**
     * Test que la réponse fournie est bien conforme au "problem"
     * ResourceNotFound
     * @param string $jsonResponse
     */
    protected function testResourceNotFoundResponse(string $jsonResponse) : void
    {
        $testedResponse = $this->testGenericApiProblemDataStructure(ResourceNotFoundException::class, $jsonResponse);
        $this->assertArrayHasKey('detail', $testedResponse);
    }

    /**
     * Test que la réponse fournie est bien conforme au "problem"
     * RouteNotFound
     * @param string $jsonResponse
     */
    protected function testRouteNotFoundResponse(string $jsonResponse) : void
    {
        $testedResponse = $this->testGenericApiProblemDataStructure(RouteNotFoundException::class, $jsonResponse);
        $this->assertArrayHasKey('detail', $testedResponse);
    }

    /**
     * Test que la réponse fournie est bien conforme au "problem"
     * MethodNotAllowed
     * @param string $jsonResponse
     */
    protected function testMethodNotAllowedResponse(string $jsonResponse) : void
    {
        $testedResponse = $this->testGenericApiProblemDataStructure(MethodNotAllowedException::class, $jsonResponse);
        $this->assertArrayHasKey('detail', $testedResponse);
    }

}
