<?php

namespace App\Library\APIProblem\Exceptions\Problems;

class BadRequestException extends APIProblemsException
{
    public function __construct(string $errorDescription)
    {
        parent::__construct();
        $this->setDetail($errorDescription);
    }
}
