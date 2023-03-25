<?php


namespace App\Library\APIProblem\Exceptions\Problems;

class UnauthenticatedUserException extends APIProblemsException
{

    public function __construct()
    {
        parent::__construct();
        $this->setDetail('Cannot authenticate current API end-user using OIDC');
    }


}
