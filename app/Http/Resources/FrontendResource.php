<?php

namespace App\Http\Resources;

use App\Models\FrontendSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontendResource extends JsonResource
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
            'frontend_hero_section_title'           => $this->info['frontend_hero_section_title'],
            'frontend_hero_section_sub_title'       => $this->info['frontend_hero_section_sub_title'],
            'frontend_app_section_title'            => $this->info['frontend_app_section_title'],
            'frontend_app_section_android_app_link' => $this->info['frontend_app_section_android_app_link'],
            'frontend_app_section_iso_app_link'     => $this->info['frontend_app_section_iso_app_link'],
            'frontend_about_title'                  => $this->info['frontend_about_title'],
            'frontend_benefit_title'                => $this->info['frontend_benefit_title'],
            'frontend_restaurant_section_title'     => $this->info['frontend_restaurant_section_title'],
            'frontend_restaurant_section_sub_title' => $this->info['frontend_restaurant_section_sub_title'],
            'frontend_delivery_section_title'       => $this->info['frontend_delivery_section_title'],
            'frontend_delivery_section_sub_title'   => $this->info['frontend_delivery_section_sub_title'],
            "frontend_hero_section_image"           => $this->frontendImage('frontend_hero_section_image')?->heroImage,
            "frontend_app_section_image"            => $this->frontendImage('frontend_app_section_image')?->appImage,
            "frontend_restaurant_section_image"     => $this->frontendImage('frontend_restaurant_section_image')?->restaurantImage,
            "frontend_delivery_section_image"       => $this->frontendImage('frontend_delivery_section_image')?->deliveryImage
        ];
    }

    public function frontendImage($key)
    {
        return FrontendSetting::where(['key' => $key])->first();
    }
}
