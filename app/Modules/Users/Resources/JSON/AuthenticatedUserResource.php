<?php

namespace App\Modules\Users\Resources\JSON;

use Domain\Users\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthenticatedUserResource extends JsonResource
{
    public function toArray(Request $request)
    {
        /** @var User $this */
        return [
            'id' => $this->id,
            'username' => $this->username,
            'last_login' => $this->last_login,
            'api_key' => $this->api_key,
            'api_key_expire_at' => $this->api_key_expire_at,
            'created_at' => $this->created_at,
        ];
    }
}
