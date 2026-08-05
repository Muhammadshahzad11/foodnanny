<?php

namespace App\Http\Requests;

use App\Rules\ValidJsonOrder;
use Illuminate\Foundation\Http\FormRequest;

class WaiterOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id'         => ['required', 'integer', 'exists:restaurant_tables,id'],
            'token'            => ['nullable', 'numeric', 'max:9999999999'],
            'subtotal'         => ['required', 'numeric'],
            'discount'         => ['nullable', 'numeric'],
            'total'            => ['required', 'numeric'],
            'tax'              => ['required', 'numeric'],
            'items'            => ['required', 'json', new ValidJsonOrder],
            'order_note'       => ['nullable', 'string', 'max:1000'],
            'send_to_kitchen'  => ['nullable', 'boolean'],
        ];
    }
}
