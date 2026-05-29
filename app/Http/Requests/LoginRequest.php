<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'exists:users',
            ],
            'password' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}
