<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    public array $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "site_date_format"                                        => $this->info['site_date_format'],
            "site_time_format"                                        => $this->info['site_time_format'],
            "site_default_timezone"                                   => $this->info['site_default_timezone'],
            'site_default_ai_agent'                                   => $this->info['site_default_ai_agent'],
            "site_default_currency"                                   => $this->info['site_default_currency'],
            "site_default_currency_symbol"                            => $this->info['site_default_currency_symbol'],
            "site_currency_position"                                  => $this->info['site_currency_position'],
            "site_digit_after_decimal_point"                          => $this->info['site_digit_after_decimal_point'],
            "site_email_verification"                                 => $this->info['site_email_verification'],
            "site_phone_verification"                                 => $this->info['site_phone_verification'],
            "site_default_language"                                   => $this->info['site_default_language'],
            "site_language_switch"                                    => $this->info['site_language_switch'],
            "site_app_debug"                                          => $this->info['site_app_debug'],
            "site_auto_localization"                                  => $this->info['site_auto_localization'],
            "site_auto_update"                                        => $this->info['site_auto_update'],
            "site_google_map_key"                                     => $this->info['site_google_map_key'],
            "site_copyright"                                          => $this->info['site_copyright'],
            "site_online_payment_gateway"                             => $this->info['site_online_payment_gateway'],
            "site_rider_tip"                                          => $this->info['site_rider_tip'],
            "site_cutlery"                                            => $this->info['site_cutlery'],
            "site_default_sms_gateway"                                => $this->info['site_default_sms_gateway'],
            "site_default_storage"                                    => $this->info['site_default_storage'],
            'site_restaurant_search_radius'                           => $this->info['site_restaurant_search_radius'],
            'site_delivery_boy_order_radius'                          => $this->info['site_delivery_boy_order_radius'],
            'site_cash_on_delivery'                                   => $this->info['site_cash_on_delivery'],
            'site_order_cancel'                                       => $this->info['site_order_cancel'] ?? \App\Enums\Activity::DISABLE,
            'site_service_fee'                                        => $this->info['site_service_fee'],
            'site_default_order_commission'                           => $this->info['site_default_order_commission'],
            'site_default_delivery_commission'                        => $this->info['site_default_delivery_commission'],
            'site_default_pos_commission'                             => $this->info['site_default_pos_commission'],
            'site_same_time_delivery_boy_maximum_orders_accept_limit' => $this->info['site_same_time_delivery_boy_maximum_orders_accept_limit'],
            'site_rating_time'                                        => $this->info['site_rating_time'],
            'site_return_order_time'                                  => $this->info['site_return_order_time'],
            'site_default_ai_data_generation_limit'                   => $this->info['site_default_ai_data_generation_limit'],
        ];
    }
}
