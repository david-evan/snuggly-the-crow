<?php

namespace App\Library\APIProblem\Exceptions\Problems;

use App\Library\APIProblem\Interfaces\RenderableException;
use Crell\ApiProblem\ApiProblem;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * APIProblemsException - Classe de base de tous les API Problems
 * @see https://tools.ietf.org/html/rfc7807
 * @package App\Exceptions\Problems
 */
abstract class APIProblemsException extends Exception implements RenderableException
{
    private const API_PROBLEMS_CONFIG_FILE = "api-problems";

    /**
     * Instance de ApiProblem à traiter
     * @var ApiProblem
     */
    protected ApiProblem $apiProblem;

    /**
     * Détail de "ApiProblem"
     * @var string
     */
    protected ?string $detail = null;

    /**
     * Attributs personnalisés
     * @var array
     */
    protected array $customAttributes = [];


    public function __construct()
    {
        parent::__construct();
        $this->apiProblem ??= new ApiProblem();
        $this->loadProblemFromConfig();
    }

    /**
     * Permet de charger la configuration du problem tel que décrite
     * dans le fichier /config/api-problems-config.php
     *
     */
    final protected function loadProblemFromConfig(): void
    {
        $configKey = self::API_PROBLEMS_CONFIG_FILE;

        $problem = config($configKey . '.problems.' . static::class) ?? config($configKey . '.default') ?? [];
        $this->apiProblem->setStatus($problem['status'] ?? 500);
        $this->apiProblem->setType($problem['type'] ?? '');
        $this->apiProblem->setTitle($problem['title'] ?? '');
    }

    /**
     * @inheritDoc
     * Prépare le rendu d'ApiProblem et renvoi la réponse.
     */
    final public function render(): Response
    {
        if (!empty($this->getDetail())) {
            $this->apiProblem->setDetail($this->getDetail());
        }

        $this->setCustomAttributesToApiProblem();

        return \response($this->apiProblem, $this->apiProblem->getStatus());
    }

    /**
     * Getter : Detail
     * @return null|string
     */
    final public function getDetail(): ?string
    {
        return $this->detail;
    }

    /**
     * ----------------------------
     * Getter / Setter
     * ----------------------------
     */

    /**
     * Setter : Details
     * @param string $detail
     * @return APIProblemsException
     */
    final public function setDetail(string $detail): APIProblemsException
    {
        $this->detail = $detail;
        return $this;
    }

    /**
     * Ajoute les customs Attributes à l'instance d'ApiProblem
     */
    private function setCustomAttributesToApiProblem(): void
    {
        foreach ($this->customAttributes as $name => $value) {
            $this->apiProblem[$name] = $value;
        }
    }

    /**
     * Ajoute un attribut personnalisé
     * @param string $name
     * @param $value
     * @return APIProblemsException
     */
    final public function addCustomAttributes(string $name, $value): APIProblemsException
    {
        $this->customAttributes[$name] = $value;
        return $this;
    }

}
