<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'confirm_password' => 'required|string|min:8|same:password',
            'password' => 'required|string|min:8|max:30',
            'confirm_password' => 'required|string|same:password'
        ];
    }
    public function messages(){
        return [
            'password.regex' => 'Your password must have 8 to 30 characters.',
            'confirm_password.same' => 'Your password and confirmation password do not match.',
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $optionalData = [];
        $optionalData['message'] = $validator->errors()->first();
        $response =  getResponse(6000,422,[],$optionalData);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
