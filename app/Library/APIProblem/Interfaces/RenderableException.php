<?php


namespace App\Library\APIProblem\Interfaces;


use Symfony\Component\HttpFoundation\Response;

/**
 * Permet de rendre une exception "renderable", c.-à-d. qui peut être affichée sous forme de message
 */
interface RenderableException
{

    /**
     * Assure le rendu de l'erreur - Doit retourner une instance de Response
     * @return Response
     */
    public function render(): Response;
}
