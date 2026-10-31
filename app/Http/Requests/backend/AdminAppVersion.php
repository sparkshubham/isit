<?php

namespace App\Http\Requests\backend;

use Illuminate\Foundation\Http\FormRequest;

class AdminAppVersion extends FormRequest
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
        // prd($request['current_version']);
        $rule =  [ 
            'current_version.android' => 'required|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'current_version.ios' => 'required|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'current_version.*' => 'required|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'min_version.android' => 'required|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'min_version.ios' => 'required|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'min_version.*' => 'required|min:0|regex:/^\d+(\.\d{1,2})?$/',
            // 'description' => 'required',
        ];
        return $rule;

    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'current_version.android.required'=>'The Current Version field is required.',
            'current_version.android.regex'=>'The Current Version is not invalid.',
            'current_version.ios.required'=>'The Current Version field is required.',
            'current_version.ios.regex'=>'The Current Version is not invalid.',
            'min_version.ios.regex'=>'The Minimum Version is not invalid.',
            'min_version.android.required'=>'The Minimum Version field is required.',
            'min_version.android.regex'=>'The Minimum Version is not invalid.',
            'min_version.ios.required'=>'The Minimum Version field is required.',
            'min_version.ios.regex'=>'The Minimum Version is not invalid.',
            ];
    }
}
