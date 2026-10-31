<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ViewStoryRequest extends FormRequest
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
            'story_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'story_id' => 'required|unique:users_stories_views,story_id,NULL,NULL,user_id, ' . $request['user_id'],
            'user_id' => 'required|unique:users_stories_views,user_id,NULL,NULL,story_id, ' . $request['story_id']
        ];
    }
    public function messages()
    {
        return [
            'story_id.unique' => 'You have already viewed this story.',
            'user_id.unique' => 'You have already viewed this story.',
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
