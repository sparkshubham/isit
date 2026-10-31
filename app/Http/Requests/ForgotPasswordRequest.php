<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'email' =>'required|email'
        ];
    }
    public function messages(){
        return [
            'email' => 'Email is required'
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $optionalData = [];
        $optionalData['message'] = $validator->errors()->first();
        $response =  getResponse(6000,422,[],$optionalData);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
