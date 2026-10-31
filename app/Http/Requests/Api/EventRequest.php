<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class EventRequest extends FormRequest
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
        return [
            'id' => 'numeric',
            'name' => 'required|min:3|max:500',
            'date' => 'required|date',
            'start_time' => 'required|date_format:Y-m-d H:i:s|after:' . Carbon::now()->format('Y-m-d H:i:s'),
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',

            /* 'start_time' => 'required',
            'end_time' => 'required', */
            'timeoffset' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'start_time.after' => 'Start time must be greater than Current time',
            'end_time.after' => 'End time must be greater than start time'
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
