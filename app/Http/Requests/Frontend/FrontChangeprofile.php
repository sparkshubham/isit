<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class FrontChangeprofile extends FormRequest
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
            'last_name' => 'required|min:2|max:50',
            'phone_number' => 'min:7|max:15', 
            'email' => 'sometimes|required|email:rfc,dns',
            // 'profile_picture' => 'nullable|mimes:jpeg,jpg,png,gif|max:10000' 
            'profile_picture' => 'nullable|mimes:jpeg,jpg,png,gif|max:10000' ,
            'bio_text' => 'min:10|max:50',
            'country_code' => 'max:10',

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
            'first_name.required'=>'The first name field is required.',
            // 'bio_text.required'=>'The description field is required.',
            'bio_text.min'=>'The bio text must be at least 10 characters.',
            'bio_text.max'=>'The bio text may not be greater than 50 characters.',
            'phone_number.required'=>'The profile_picture field is required.',
            'first_name.min'=>'The first name must be at least 3 characters.',
            'first_name.max'=>'The first name must should not be greater than 50 characters.',
            ];
    }
}
