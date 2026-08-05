<?php

namespace App\Http\Requests;

use App\Enums\CampaignStatus;
use Illuminate\Foundation\Http\FormRequest;

class CampaignRestaurantVerifyRequest extends FormRequest
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
            'status' => ['required', 'numeric', 'in:'.CampaignStatus::APPROVE.','.CampaignStatus::REJECT],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => trans('all.message.verify_field_is_required')
        ];
    }
}
