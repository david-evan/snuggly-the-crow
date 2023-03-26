<?php

namespace App\Modules\Blog\Requests;

use Domain\Blog\ValueObjects\Status;
use Illuminate\Validation\Rules\Enum;

class GetArticles extends ArticleRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return  [
            'status' =>  ['sometimes', new Enum(Status::class)],
            'perPage' => 'sometimes|required|integer',
        ];
    }
}
