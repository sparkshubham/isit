<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class GeneralPostRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'heading' => 'required|string|min:3|max:50',
            'description' => 'required|string|min:10|max:100',
        ];
    }
    public function messages(){
        return [
            'heading' => 'Please enter heading',
            'description' => 'Please enter description'
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $optionalData = [];
        $optionalData['message'] = $validator->errors()->first();
        $response =  getResponse(6000,422,[],$optionalData);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
