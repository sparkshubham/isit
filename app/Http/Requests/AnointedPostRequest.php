<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class AnointedPostRequest extends FormRequest{

    public function authorize(){
        return true;
    }

    public function rules(){
        return [
            // 'id' => 'numeric',
            // 'heading' => 'required',
            'description' => 'required_with:file|min:10|max:1000',
            'category' => 'required',
            'country' => 'required',
            'city_name' => 'required|min:2|max:50',
            'zip_code' => 'required|min:5|max:9',
            'file' => 'max:100040', 
        ];
    }
    public function messages(){
        return [
            'category' => 'Please Select Category',
            'country' => 'Please Select Country',
            'description.required_with'=>'The anointed details field is required when file is present.'
        ];
    }

}
