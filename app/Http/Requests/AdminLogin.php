<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLogin extends FormRequest
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
        return [
            'username'=>['bail','required'],
            'password'=>['bail','required']
        ];
    }

    public function messages()
    {
        return [
            'required'=> '请填写:attribute'
        ];
    }

    public function attributes(){
        return [
            'username'=>'用户名',
            'password'=>'密码'
        ];
    }
}
