<?php

namespace App\Modules\Common\Requests;

use App\Library\APIProblem\Exceptions\Problems\RequestValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * APIRequest Base
 * Class ApiRequest
 * @package App\Http\Requests
 */
abstract class ApiRequest extends FormRequest
{

    public function wantsJson()
    {
        return true;
    }

    public function expectsJson()
    {
        return true;
    }

    /**
     * Comportement en cas d'échec de validation
     * @param Validator $validator
     * @throws RequestValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new RequestValidationException($validator);
    }
}
