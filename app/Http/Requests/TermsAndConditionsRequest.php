<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermsAndConditionsRequest extends FormRequest
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
            'terms_and_conditions_customer_page_id'     => 'nullable|numeric',
            'terms_and_conditions_restaurant_page_id'   => 'nullable|numeric',
            'terms_and_conditions_delivery_boy_page_id' => 'nullable|numeric'
        ];
    }
}
