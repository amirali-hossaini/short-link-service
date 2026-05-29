<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUrlRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'origin_url' => [
                'required',
                'url',
                'max:2048',
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after:now',
            ],
            'alias' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('urls', 'short_code'),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('alias')) {
            $this->merge([
                'alias' => strtolower($this->alias),
            ]);
        }
    }
}
