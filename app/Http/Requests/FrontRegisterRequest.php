<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class FrontRegisterRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'first_name' => 'required|min:3|max:50',
            'last_name' => 'required|min:3|max:50',
            'email' =>'required|email|unique:users,email,NULL,id',
            'password' => 'required|string|min:8|max:30',
            'confirm_password' => 'required|string|same:password',
            // 'password' => 'required|string|min:8|max:30|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            // 'confirm_password' => 'required|string|min:8|max:15|same:password|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'term_n_condition' => 'required',
            'phone_number' => 'min:7|max:15',
        ];
    }
    public function messages(){
        return [
            'first_name' => 'First Name is required',
            'last_name' => 'Last Name is required',
            'email' => 'Please enter a valid email address.',
            'password.regex' => 'The password should be at least 8 characters long.',
            'confirm_password.same' => 'Your password and confirm password does not match.',
            'term_n_condition.required' => 'The terms and conditions and privacy policy field is required.',
            'last_name' => 'Last Name is required',
            
            
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $optionalData = [];
        $optionalData['message'] = $validator->errors()->first();
        $response =  getResponse(6000,422,[],$optionalData);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
