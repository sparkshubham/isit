<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PasswordChangeRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|max:30',
            'confirm_password' => 'required|string|min:8|max:15|same:password'
        ];
    }
    public function messages(){
        return [
            'old_password' => 'Old Password is required',
            'new_password.regex' => 'The password should be at least 8 characters long,',
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
