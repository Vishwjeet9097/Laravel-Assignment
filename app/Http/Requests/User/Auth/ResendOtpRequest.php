<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResendOtpRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|exists:users,email',
        ];
    }
    public function messages()
    {

        return [
            'email.exists' => 'The email provided is not registered.'
        ];
    }
}
