<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => array('required'),
            'email' => array('required'),
            'message' => array('required'),
        ];
    }

    public function failedValidation(Validator $validator):HttpResponseException
    {

        throw new HttpResponseException(response()->json([

            'status'   => false,

            'action'   => 'Validation errors',

            'errors'      => $validator->errors()->all()

        ]));
    }
}
