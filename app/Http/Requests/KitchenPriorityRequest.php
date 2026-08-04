<?php

namespace App\Http\Requests;

use App\Enums\KitchenPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KitchenPriorityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kitchen_priority' => [
                'required',
                'integer',
                Rule::in([
                    KitchenPriority::NORMAL,
                    KitchenPriority::HIGH,
                    KitchenPriority::URGENT,
                    KitchenPriority::VIP,
                ]),
            ],
        ];
    }
}
