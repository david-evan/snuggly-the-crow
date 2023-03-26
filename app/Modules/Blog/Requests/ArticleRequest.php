<?php

namespace App\Modules\Blog\Requests;

use App\Library\APIProblem\Http\Requests\API\ApiRequest;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;

abstract class ArticleRequest extends ApiRequest
{

}
