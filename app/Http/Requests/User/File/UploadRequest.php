<?php

namespace App\Http\Requests\User\File;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    public function rules()
    {
        return [
            'file' => 'required',
            // 'file' => 'required',
            'fileType' => 'required',
            'type' => 'required',
        ];
    }
}