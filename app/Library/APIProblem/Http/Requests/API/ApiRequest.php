<?php


namespace App\Library\APIProblem\Http\Requests\API;


use App\Library\APIProblem\Exceptions\Problems\RequestValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * APIRequest Base
 * @package App\Http\Requests
 */
abstract class ApiRequest extends FormRequest
{
    /**
     * Force la requête à être traitée comme une requête JSON
     * @return bool
     */
    public function wantsJson(): bool
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
