<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    protected $inputs = [];

    public function authorize()
    {
        return true;
    }

    abstract function rules();

    /**
     * The data to be validated should be processed as JSON.
     * @return mixed
     */
    public function validationData()
    {
        $inputs = array_replace_recursive(
            $this->json()->all(),
            $this->all(),
            $this->route()->parameters()
        );
        $this->inputs = array_merge($this->inputs, $inputs);
        return $this->inputs;
    }

    /**
     * Add extra variable(s) in the input request data. Can be used in any child Request Class.
     * @param $array
     */
    protected function add($array)
    {
        $this->inputs = array_merge($this->inputs, $array);
    }
}
