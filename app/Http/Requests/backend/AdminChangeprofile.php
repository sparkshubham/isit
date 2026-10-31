<?php

namespace App\Http\Requests\backend;

use Illuminate\Foundation\Http\FormRequest;

class AdminChangeprofile extends FormRequest
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
            'first_name' => 'required|min:3|max:50',
            'last_name' => 'max:50',
            /* 'phone_number' => 'required', */
            'email' => 'sometimes|required|email:rfc,dns',
            //'profile_picture' => 'required_with:id|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'profile_picture' => 'required_with:id|image|mimes:jpeg,png,jpg',
        ];
    }
    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required'=>'The Name field is required.',
            'first_name.min'=>'The Name must be at least 3 characters.',
            'first_name.max'=>'The Name must should not be greater than 50 characters.',
            ];
    }
}
