<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CampaignRestaurantRequest extends FormRequest
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
            'restaurant_id'   => ['required','numeric', Rule::unique("campaign_restaurants", "restaurant_id")->ignore($this->route('campaignRestaurant.id'))->where('campaign_id', $this->route('campaign.id'))]
        ];
    }

    public function attributes(): array
    {
        return [
            'restaurant_id' => strtolower(trans('all.label.restaurant'))
        ];
    }
}
