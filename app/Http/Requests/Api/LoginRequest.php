<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:30',
            'device_type' => ['required','in:1,2,3'],//1:ios;2:android,3-Web
            'device_id' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'password.min' => 'Invalid Credentials.',
            'password.max' => 'Invalid Credentials.',
            'password.regex' => 'Your password must have 8 to 15 characters.',
            'confirm_password.same' => 'Confirm password is not same as new password'
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $optionalData = [];
        $optionalData['message'] = $validator->errors()->first();
        $response =  getResponse(6000,422,[],$optionalData);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
