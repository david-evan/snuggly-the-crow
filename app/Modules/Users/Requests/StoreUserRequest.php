<?php

namespace App\Modules\Users\Requests;

class StoreUserRequest extends UserRequest
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
            'username' => 'required|max:128|min:3',
            'password' => 'required|max:128|min:6',
        ];
    }
}
