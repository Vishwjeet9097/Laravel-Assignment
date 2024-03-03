<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{

    public function rules()
    {
        return [
            'email' => 'required|exists:users,email',
            'otp' => 'required|digits:4'
        ];
    }
    public function messages()
    {
        return [
            'email.exists' => 'The email provided is not registered.'
        ];
    }
}
