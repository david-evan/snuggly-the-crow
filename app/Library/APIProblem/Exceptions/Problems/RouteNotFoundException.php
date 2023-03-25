<?php

namespace App\Library\APIProblem\Exceptions\Problems;

class RouteNotFoundException extends APIProblemsException
{

    public function __construct()
    {
        parent::__construct();
        $this->setDetail('The API `' . request()->getPathInfo() . '` you looking for appear to be not existing');
    }


}
