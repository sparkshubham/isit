<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UserBlockRequest extends FormRequest
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
        $user = Auth::guard('web')->user();
        $request = request();
        $blockUserId = 0;
        if(isset($request->block_user_id)){
            $blockUserId = $request->block_user_id;
        }
        return [
            // 'user_id' => 'required|numeric',
            'block_user_id' => 'required|numeric',            
            'block_user_id' => 'required|unique:block_users,block_user_id,NULL,NULL,user_id, ' . $user->id,
            'user_id' => 'unique:block_users,user_id,NULL,NULL,block_user_id, ' . $blockUserId
        ];
    }
    public function messages()
    {
        return [
            'block_user_id.unique' => 'You are not authorize to perform this action.',
            'user_id.unique' => 'You have already blocked this user.',
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

    
}
