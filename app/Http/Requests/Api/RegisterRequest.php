<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
    // |regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/
    public function rules()
    {
        return [
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'email' =>'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string|min:8|max:30',
            'device_type' => ['required','in:1,2,3'],//1:ios;2:android,3-Web
            'phone_number' => 'min:7|max:15',
            'dob' => 'nullable|date_format:Y-m-d|before:today',
            // 'phone_number' => 'min:9|max:30',
            'confirm_password' => 'required|string|min:8|max:30|same:password'
        ];
    }
    public function messages()
    {
        return [
            'password.regex' => 'Your password must have 8 to 30 characters.',
            'confirm_password.same' => 'Confirm password is not same as new password'
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

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $optionalData = [];
        $optionalData['message'] = $validator->errors()->first();
        $response =  getResponse(6000,422,[],$optionalData);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
