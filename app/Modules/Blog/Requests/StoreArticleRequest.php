<?php

namespace App\Modules\Blog\Requests;

use Domain\Blog\ValueObjects\Status;
use Illuminate\Validation\Rules\Enum;

class StoreArticleRequest extends ArticleRequest
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
        return [
            'title' => 'required|max:128|min:3',
            'content' => 'required',
            'status' => ['required', new Enum(Status::class)],
            'published_at' => 'sometimes|date_format:' . DATE_RFC3339,
        ];
    }
}
