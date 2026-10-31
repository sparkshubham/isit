<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class AnointedPostRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'category' =>'required',
            'country' =>'required'
        ];
    }
    public function messages(){
        return [
            'category' => 'Please Select Category',
            'country' => 'Please Select Country'
        ];
    }
}
