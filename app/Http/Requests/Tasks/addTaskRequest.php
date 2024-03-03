<?php

namespace App\Http\Requests\Tasks;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class addTaskRequest extends BaseRequest
{
    public function rules()
    {
        return [
            "project_id" => "required|exists:projects,id",
            "title" => "required",
            "priority" => "required|in:0,1,2",
        ];
    }
}
