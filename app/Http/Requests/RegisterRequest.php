<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
            ],
        ];
    }
}
