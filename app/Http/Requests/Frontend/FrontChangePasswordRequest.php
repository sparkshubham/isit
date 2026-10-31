<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class FrontChangePasswordRequest extends FormRequest
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
  
    // public function rules()
    // {
    //     return [
    //         'old_password' => 'required',
    //         // 'new_password' => 'required|min:8|max:32',
    //          'new_password' => 'required|string|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            
    //         'confirm_password' => 'required|string|required_with:new_password|same:new_password',

    //         // 'old_password' => 'required',
    //         // 'new_password' => 'required|string|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
    //         // 'confirm_password' => 'required|string|min:8|max:15|same:password|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
   
    //     ];
    // }


    // public function messages()
    // {
    //     return [
    //         'password.required' => 'The password should be at least 8 characters long, and must contain an uppercase character, a lowercase character, a number and a special character.',
    //         'confirm_password.same' => 'Your password and confirmation password do not match.',
    //     ];
    // }

    public function rules()
    {
        return [
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|max:32',
            'confirm_password' => 'required|string|min:8|max:32|same:new_password'
        ];
    }

    public function messages()
    {
        return [
            'new_password.regex' => 'The password should be at least 8 characters long.',
            'confirm_password.same' => 'Your password and confirmation password do not match.',
            'old_password.required'=>'The Current password field is required.',
            
        ];
    }

}

