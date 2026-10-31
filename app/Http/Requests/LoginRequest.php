<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        return [
            'email' =>'required|email',
            'password' => 'required|string',
        ];
    }
    public function messages(){
        return [
            'password.regex' => 'The password should be at least 8 characters long.',
            'email' => 'Email address is required',
            'email.email' => 'Invalid email address.'

        ];
    }
    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    /*public function messages()
    {
        return ['image.required_without'=>'The :attribute field is required.'];
    }*/

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        $optionalData = [];
        $optionalData['message'] = $validator->errors()->first();
        $response =  getResponse(6000,422,[],$optionalData);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
