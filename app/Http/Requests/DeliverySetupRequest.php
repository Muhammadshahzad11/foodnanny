<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliverySetupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'delivery_setup_free_delivery_kilometer' => ['required', 'numeric'],
            'delivery_setup_basic_delivery_fee'      => ['required', 'numeric'],
            'delivery_setup_charge_per_kilo'         => ['required', 'numeric']
        ];
    }
}
