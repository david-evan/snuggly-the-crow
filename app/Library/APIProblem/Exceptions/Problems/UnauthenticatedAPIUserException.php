<?php


namespace App\Library\APIProblem\Exceptions\Problems;

class UnauthenticatedAPIUserException extends APIProblemsException
{

    public function __construct()
    {
        parent::__construct();
        $this->setDetail('Cannot authenticate current API user by API Key');
    }


}
