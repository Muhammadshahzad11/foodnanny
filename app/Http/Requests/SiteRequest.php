<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteRequest extends FormRequest
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
            'site_date_format'                                        => ['required', 'string', 'max:190'],
            'site_time_format'                                        => ['required', 'string', 'max:190'],
            'site_default_timezone'                                   => ['required', 'string', 'max:190'],
            'site_default_ai_agent'                                   => ['nullable', 'numeric'],
            'site_default_currency'                                   => ['required', 'numeric'],
            'site_default_ai_data_generation_limit'                   => ['required', 'numeric'],
            'site_currency_position'                                  => ['required', 'numeric'],
            'site_digit_after_decimal_point'                          => ['required', 'numeric', 'min:1', 'max:6'],
            'site_email_verification'                                 => ['required', 'numeric'],
            'site_phone_verification'                                 => ['required', 'numeric'],
            'site_default_language'                                   => ['required', 'numeric'],
            'site_language_switch'                                    => ['required', 'numeric'],
            'site_app_debug'                                          => ['required', 'numeric'],
            'site_auto_localization'                                  => ['required', 'numeric'],
            'site_auto_update'                                        => ['required', 'numeric'],
            'site_google_map_key'                                     => ['required', 'string', 'max:190'],
            'site_copyright'                                          => ['required', 'string', 'max:190'],
            'site_online_payment_gateway'                             => ['required', 'numeric'],
            'site_rider_tip'                                          => ['required', 'numeric'],
            'site_cutlery'                                            => ['required', 'numeric'],
            'site_default_sms_gateway'                                => ['nullable', 'numeric'],
            'site_default_storage'                                    => ['required', 'numeric'],
            'site_restaurant_search_radius'                           => ['required', 'numeric'],
            'site_delivery_boy_order_radius'                          => ['required', 'numeric'],
            'site_cash_on_delivery'                                   => ['required', 'numeric'],
            'site_service_fee'                                        => ['required', 'numeric'],
            'site_default_order_commission'                           => ['required', 'numeric', 'max:100'],
            'site_default_delivery_commission'                        => ['required', 'numeric', 'max:100'],
            'site_default_pos_commission'                             => ['required', 'numeric', 'max:100'],
            'site_same_time_delivery_boy_maximum_orders_accept_limit' => ['required', 'numeric'],
            'site_rating_time'                                        => ['required', 'numeric'],
            'site_return_order_time'                                  => ['required', 'numeric']
        ];
    }
}
