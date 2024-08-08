<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OtpVerifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|exists:otp_verifies,email',
            'otp' => 'required|min:6'
        ];
    }

 
    public function messages()
    {
        return [
            'email.required' => 'Please enter the Email Address',
            'email.email' => 'Please enter a valid Email Address',
            'email.exists' => "Oops! We couldn't find this email address in our records",
            'otp.required' => 'Please enter the OTP Code',
            'otp.min' => 'Please enter atleast 6 digit',
        ];
    }


    public function failedValidation(Validator $validator)
    {
        $errors = [];

        foreach ($validator->errors()->messages() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message,
                ];
            }
        }


        throw new HttpResponseException(response()->json([
            'status'   => false,
            'title' => 'Validation Error!',
            'errors' => $errors
        ]));
    }
}
