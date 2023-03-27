<?php

namespace App\Modules\Blog\Resources\JSON;

use App\Modules\Users\Resources\JSON\UserResource;

use Domain\Users\Entities\User;
use Illuminate\Http\Request;


class AuthorResource extends UserResource
{
    public function toArray(Request $request)
    {
        /** @var User $user */
        $user = $this->resource;
        return [
            'id' => $user->id,
            'username' => $user->username,
        ];
    }
}
