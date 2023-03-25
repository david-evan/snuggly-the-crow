<?php

namespace App\Library\APIProblem\Exceptions\Problems;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class MethodNotAllowedException extends APIProblemsException
{

    /**
     * MethodNotAllowedException constructor.
     * @param MethodNotAllowedHttpException $methodNotAllowedException
     */
    public function __construct(MethodNotAllowedHttpException $methodNotAllowedException)
    {
        parent::__construct();
        $this->setDetail($methodNotAllowedException->getMessage());
    }


}
