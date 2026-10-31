<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
class UserFollowRequest extends FormRequest
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
        return [
            // 'user_id' => 'required|numeric',
            'follower_id' => 'required|numeric',            
            'follower_id' => 'required|unique:users_follows,follower_id,NULL,NULL,user_id, ' . $user->id
        ];
    }
    public function messages()
    {
        return [
            'follower_id.unique' => 'You have already followed this user.',
         ];
    }
  
    
}
