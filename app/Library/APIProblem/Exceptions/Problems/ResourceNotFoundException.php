<?php

namespace App\Library\APIProblem\Exceptions\Problems;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use ReflectionClass;
use ReflectionException;

class ResourceNotFoundException extends APIProblemsException
{

    /**
     * ResourceNotFoundException constructor.
     * @param ModelNotFoundException $modelNotFoundException
     * @throws ReflectionException
     */
    public function __construct(ModelNotFoundException $modelNotFoundException)
    {
        parent::__construct();

        $model = (new ReflectionClass($modelNotFoundException->getModel()))->getShortName();
        $ids = implode(' | ', $modelNotFoundException->getIds());
        $this->setDetail('[' . $model . '] `' . $ids . '` appear to be not existing');
    }


}
