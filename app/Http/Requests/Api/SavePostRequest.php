<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SavePostRequest extends FormRequest
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
        $request = request();
        return [
            'post_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'post_id' => 'required|unique:saved_posts,post_id,NULL,NULL,user_id, ' . $request['user_id'],
            'user_id' => 'required|unique:saved_posts,user_id,NULL,NULL,post_id, ' . $request['post_id']
        ];
    }
    public function messages()
    {
        return [
            'post_id.unique' => 'You have already saved this post.',
            'user_id.unique' => 'You have already saved this post.',
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
