<?php

namespace App\Library\APIProblem\Exceptions\Problems;

use Illuminate\Contracts\Validation\Validator;

class RequestValidationException extends APIProblemsException
{

    /**
     * RequestValidationException constructor.
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        parent::__construct();

        $invalidParams = [];
        foreach ($validator->errors()->messages() as $paramName => $failedReason) {
            $invalidParams[] = ['name' => $paramName, 'reasons' => $failedReason];
        }

        $this->addCustomAttributes('invalid-params', $invalidParams);
    }


}
