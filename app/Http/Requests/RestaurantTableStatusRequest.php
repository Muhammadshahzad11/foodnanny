<?php

namespace App\Http\Requests;

use App\Enums\TableStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantTableStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'integer',
                Rule::in([
                    TableStatus::AVAILABLE,
                    TableStatus::OCCUPIED,
                    TableStatus::RESERVED,
                    TableStatus::CLEANING,
                    TableStatus::OUT_OF_SERVICE,
                    TableStatus::INACTIVE,
                ]),
            ],
        ];
    }
}
