<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUrlRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'origin_url' => [
                'sometimes',
                'url',
                'max:2048',
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after:now',
            ],
            'alias' => [
                'sometimes',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('urls', 'short_code')
                    ->ignore($this->url->id),
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
