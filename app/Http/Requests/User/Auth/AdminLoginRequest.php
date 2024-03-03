<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminLoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            "email" => [
                "required",
                Rule::exists("users")->where(function ($query) {
                    $query->whereIn("user_type", ["admin", "super_admin"]);
                }),
            ],
            "password" => "required",
        ];
    }

    public function messages()
    {
        return [
            "email.exists" =>
                "The email provided is not registered as an admin.",
        ];
    }
}
