<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class FrontVerifyRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'otp' => 'required'
        ];
    }
    public function messages(){
        return [
            'otp' => 'OTP is required'
        ];
    }
}
