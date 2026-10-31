<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LikeCommentRequest extends FormRequest
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
            'comment_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'comment_id' => 'required|unique:comments_likes,comment_id,NULL,NULL,user_id, ' . $request['user_id'],
            'user_id' => 'required|unique:comments_likes,user_id,NULL,NULL,comment_id, ' . $request['comment_id']
        ];
    }
    public function messages()
    {
        return [
            'comment_id.unique' => 'You have already liked this comment.',
            'user_id.unique' => 'You have already liked this comment.',
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
