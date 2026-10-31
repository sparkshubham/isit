<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Request;
use App\Rules\PhotoVideoValidation;



class GeneralPostRequest extends FormRequest
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
    public function rules(Request $request)
    {
            $request = request();
            $rule = [
                'id' => 'numeric',
                'heading'=>'max:50',
                'description' => 'min:10|max:1000',   
                'file' => 'max:100040', 
            ];
            
            if($request->file=='')
            {
                if($request->heading)
                {
                    if($request->file=='' && $request->description='')
                    {
                       $rule['description'] = ['required'];
                    }
                }
            }

            if($request->description=='' && $request->heading==''){
                $rule['file'] = ['required','mimes:jpg,png,jpeg|max:10000'];
            }
           

            return $rule;
    }
    public function messages()
    {
        return [
    
        ];
    }
}
