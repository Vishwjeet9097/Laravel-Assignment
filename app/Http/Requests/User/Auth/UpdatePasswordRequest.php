<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{

    public function rules()
    {
        return [
            'email' => 'required',
            'password' => 'required'
        ];
    }
}
