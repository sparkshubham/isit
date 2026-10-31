<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Auth;
class PostMediaRequest extends FormRequest
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
        $rule =  [
            'post_id' => 'required|numeric',
            'media_type' => 'required|numeric',
            'post_media_id' => 'numeric'
        ];
        if($request->media_type == 1){
            $rule['file'] = ['image','required','mimes:jpg,png,jpeg,gif'];
        }else if($request->media_type == 2){
            // 
            $rule['file.0'] = ['required','mimes:mp4,ogx,oga,ogv,ogg,webm,mov,octet-stream,m4v','mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4,application/octet-stream'];
            $rule['file.1'] = ['image','required','mimes:jpg,png,jpeg,gif'];
        }
        return $rule;
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
