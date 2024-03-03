<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{

    public function rules()
    {
        return [
            "email" => 'required|exists:users,email'
        ];
    }
}
