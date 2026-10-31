<?php

namespace App\Http\Requests\backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Auth;
class AdminPostRequest extends FormRequest
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
            // 'post_id' => 'numeric',
            'media_type' => 'required|numeric',
            'post_media_id' => 'numeric',
            'description' => 'nullable|min:10|max:5000',
        ];
        if($request->media_type == 1){
            if($request->description == ''){
                $rule['file'] = ['image','required','mimes:jpg,png,jpeg,gif'];
            }else{
                $rule['file'] = ['image','mimes:jpg,png,jpeg,gif'];
            }
            
        }else if($request->media_type == 2){
            if($request->description == ''){
                // echo $request->file;exit;
                $rule['file'] = ['required','mimes:mp4,ogx,oga,ogv,ogg,webm,mov','mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'];
            }else{
                $rule['file'] = ['mimes:mp4,ogx,oga,ogv,ogg,webm,mov','mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'];
            }
        }
        return $rule;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $request = request();
        if($request->media_type == 1){
            return ['file.mimes'=>'Please select only image jpg,png,jpeg,gif'];
        } else if($request->media_type == 2){
            return ['file.mimes'=>'Please select only video mp4,ogx,oga,ogv,ogg,webm,mov'];
        }
    }
}
