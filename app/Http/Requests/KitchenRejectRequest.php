<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KitchenRejectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason'     => ['nullable', 'string', 'max:500'],
            'updated_at' => ['nullable', 'string'],
        ];
    }
}
