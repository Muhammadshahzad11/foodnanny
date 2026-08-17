<?php

namespace App\Http\Resources;


use App\Models\FrontendSetting;
use App\Models\ThemeSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
            'company_name'                              => $this->info['company_name'],
            'company_email'                             => $this->info['company_email'],
            'company_phone'                             => $this->info['company_phone'],
            'company_address'                           => $this->info['company_address'],
            'company_country_code'                      => $this->info['company_country_code'],
            'site_default_language'                     => $this->info['site_default_language'],
            'site_copyright'                            => $this->info['site_copyright'],
            'site_currency_position'                    => $this->info['site_currency_position'],
            'site_digit_after_decimal_point'            => $this->info['site_digit_after_decimal_point'],
            'site_default_currency_symbol'              => $this->info['site_default_currency_symbol'],
            'site_phone_verification'                   => $this->info['site_phone_verification'],
            'site_email_verification'                   => $this->info['site_email_verification'],
            'site_language_switch'                      => $this->info['site_language_switch'],
            'site_auto_localization'                    => $this->info['site_auto_localization'],
            'site_cash_on_delivery'                     => $this->info['site_cash_on_delivery'],
            'site_order_cancel'                         => $this->info['site_order_cancel'] ?? \App\Enums\Activity::DISABLE,
            'site_online_payment_gateway'               => $this->info['site_online_payment_gateway'],
            'site_service_fee'                          => $this->info['site_service_fee'],
            'site_rider_tip'                            => $this->info['site_rider_tip'],
            'site_cutlery'                              => $this->info['site_cutlery'],
            'site_default_currency'                     => $this->info['site_default_currency'],
            'site_restaurant_search_radius'             => $this->info['site_restaurant_search_radius'],
            'site_delivery_boy_order_radius'            => $this->info['site_delivery_boy_order_radius'],
            'theme_logo'                                => $this->themeImage('theme_logo')->logo,
            'theme_footer_logo'                         => $this->themeImage('theme_footer_logo')->footerLogo,
            'theme_favicon_logo'                        => $this->themeImage('theme_favicon_logo')->faviconLogo,
            'theme_primary_color'                       => $this->info['theme_primary_color'] ?? null,
            'theme_secondary_color'                     => $this->info['theme_secondary_color'] ?? null,
            'otp_type'                                  => $this->info['otp_type'],
            'otp_digit_limit'                           => $this->info['otp_digit_limit'],
            'otp_expire_time'                           => $this->info['otp_expire_time'],
            'social_media_facebook'                     => $this->info['social_media_facebook'],
            'social_media_instagram'                    => $this->info['social_media_instagram'],
            'social_media_twitter'                      => $this->info['social_media_twitter'],
            'social_media_youtube'                      => $this->info['social_media_youtube'],
            'delivery_setup_free_delivery_kilometer'    => $this->info['delivery_setup_free_delivery_kilometer'],
            'delivery_setup_basic_delivery_fee'         => $this->info['delivery_setup_basic_delivery_fee'],
            'delivery_setup_charge_per_kilo'            => $this->info['delivery_setup_charge_per_kilo'],
            'cookies_details_page_id'                   => $this->info['cookies_details_page_id'],
            'cookies_summary'                           => $this->info['cookies_summary'],
            'notification_fcm_api_key'                  => $this->info['notification_fcm_api_key'],
            'notification_fcm_auth_domain'              => $this->info['notification_fcm_auth_domain'],
            'notification_fcm_project_id'               => $this->info['notification_fcm_project_id'],
            'notification_fcm_storage_bucket'           => $this->info['notification_fcm_storage_bucket'],
            'notification_fcm_messaging_sender_id'      => $this->info['notification_fcm_messaging_sender_id'],
            'notification_fcm_app_id'                   => $this->info['notification_fcm_app_id'],
            'notification_fcm_measurement_id'           => $this->info['notification_fcm_measurement_id'],
            'notification_fcm_public_vapid_key'         => $this->info['notification_fcm_public_vapid_key'],
            'notification_audio'                        => asset('/audio/notification.mp3'),
            'terms_and_conditions_customer_page_id'     => $this->info['terms_and_conditions_customer_page_id'],
            'terms_and_conditions_restaurant_page_id'   => $this->info['terms_and_conditions_restaurant_page_id'],
            'terms_and_conditions_delivery_boy_page_id' => $this->info['terms_and_conditions_delivery_boy_page_id'],
            'frontend_hero_section_title'               => $this->info['frontend_hero_section_title'],
            'frontend_hero_section_sub_title'           => $this->info['frontend_hero_section_sub_title'],
            'frontend_app_section_title'                => $this->info['frontend_app_section_title'],
            'frontend_app_section_android_app_link'     => $this->info['frontend_app_section_android_app_link'],
            'frontend_app_section_iso_app_link'         => $this->info['frontend_app_section_iso_app_link'],
            'frontend_about_title'                      => $this->info['frontend_about_title'],
            'frontend_benefit_title'                    => $this->info['frontend_benefit_title'],
            'frontend_restaurant_section_title'         => $this->info['frontend_restaurant_section_title'],
            'frontend_restaurant_section_sub_title'     => $this->info['frontend_restaurant_section_sub_title'],
            'frontend_delivery_section_title'           => $this->info['frontend_delivery_section_title'],
            'frontend_delivery_section_sub_title'       => $this->info['frontend_delivery_section_sub_title'],
            "frontend_hero_section_image"               => $this->frontendImage('frontend_hero_section_image')?->heroImage,
            "frontend_app_section_image"                => $this->frontendImage('frontend_app_section_image')?->appImage,
            "frontend_restaurant_section_image"         => $this->frontendImage('frontend_restaurant_section_image')?->restaurantImage,
            "frontend_delivery_section_image"           => $this->frontendImage('frontend_delivery_section_image')?->deliveryImage,
            'profile_cover'                             => asset('/images/required/profile_cover.jpg'),
            'image_app_store'                           => asset('/images/required/app-store.png'),
            'image_play_store'                          => asset('/images/required/play-store.png'),
            'image_vag'                                 => asset('/images/required/veg.png'),
            'image_non_vag'                             => asset('/images/required/non-veg.png'),
            'image_cart'                                => asset('/images/required/empty-cart.gif'),
            'image_restaurant'                          => asset('/images/required/empty-restaurant.gif'),
            'image_order_track'                         => asset('/images/required/track.png'),
            'image_confirm'                             => asset('/images/required/confirm.gif'),
            'image_order_placed'                        => asset('/images/required/placed.gif'),
            'image_order_complete'                      => asset('/images/required/complete.gif'),
            'image_order_delivered'                     => asset('/images/required/delivered.gif'),
            'image_order_preparing'                     => asset('/images/required/preparing_order.gif'),
            'image_order_prepared'                      => asset('/images/required/prepared_order.gif'),
            'image_order_out_for_delivery'              => asset('/images/required/out_for_delivery.gif'),
            'image_order_rejected'                      => asset('/images/required/rejected.gif'),
            'image_order_canceled'                      => asset('/images/required/canceled.gif'),
            'image_order_returned'                      => asset('/images/required/returned.gif'),
            'image_four_zero_four_page'                 => asset('/images/required/404.gif'),
            'image_four_zero_three_page'                => asset('/images/required/403.gif'),
            'image_halal'                               => asset('/images/required/halal.png'),
            'image_offer'                               => asset('/images/required/offer.png'),
            'image_delivery'                            => asset('/images/required/delivery.png'),
            'image_active_orders'                       => asset('/images/required/active-orders.png'),
            'image_previous_orders'                     => asset('/images/required/previous-orders.png'),
            'data_not_found'                            => asset('/images/required/data_not_found.png'),
            'image_address'                             => asset('/images/required/address.gif'),
            'image_403'                                 => asset('/images/required/403.gif'),
            'image_404'                                 => asset('/images/required/404.gif')
        ];
    }

    public function themeImage($key)
    {
        return ThemeSetting::where(['key' => $key])->first();
    }

    public function frontendImage($key)
    {
        return FrontendSetting::where(['key' => $key])->first();
    }
}
