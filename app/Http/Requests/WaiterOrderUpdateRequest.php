<?php

namespace App\Http\Requests;

use App\Rules\ValidJsonOrder;
use Illuminate\Foundation\Http\FormRequest;

class WaiterOrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'      => ['nullable', 'numeric', 'max:9999999999'],
            'subtotal'   => ['required', 'numeric'],
            'discount'   => ['nullable', 'numeric'],
            'total'      => ['required', 'numeric'],
            'tax'        => ['required', 'numeric'],
            'items'      => ['required', 'json', new ValidJsonOrder],
            'order_note' => ['nullable', 'string', 'max:1000'],
            'updated_at' => ['nullable', 'date'],
        ];
    }
}
