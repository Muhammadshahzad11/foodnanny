<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations'     => ['nullable', 'array'],
            'translations.*'   => ['array'],
            'translations.*.*' => ['nullable', 'string'],
        ];
    }
}
