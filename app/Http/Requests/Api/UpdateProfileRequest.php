<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Auth;
class UpdateProfileRequest extends FormRequest
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
        $user = Auth::user();
        $id = $user->id;
        $rules = [
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'bio_text' => 'max:50',
            'country_code' => 'max:10',
            // 'phone_number' => 'min:7|max:10',
            'phone_number' => 'required|min:7|max:15|unique:users,phone_number,'.$id.',id,deleted_at,NULL',
            // 'phone_number' => 'max:10',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:10000', // max 10000kb
            'username' => 'required|min:3|max:100|unique:users,username,'.$id.',id,deleted_at,NULL',
            'dob' => 'nullable|date_format:Y-m-d|before:today',
        ];

        return $rules;
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

    public function messages() {
        return [
           'phone_number.unique' => 'Phone number is already linked with another user.',
        ];
    }
}
