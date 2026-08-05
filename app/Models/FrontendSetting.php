<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FrontendSetting extends Model implements HasMedia
{
    use InteractsWithMedia, Translatable;

    protected $table = "settings";

    public function getHeroImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('frontend-hero-section-image'))) {
            return asset($this->getFirstMediaUrl('frontend-hero-section-image'));
        }
        return asset('images/default/frontend/frontend-hero-section-image.png');
    }

    public function getAppImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('frontend-app-section-image'))) {
            return asset($this->getFirstMediaUrl('frontend-app-section-image'));
        }
        return asset('images/default/frontend/frontend-app-section-image.png');
    }

    public function getRestaurantImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('frontend-restaurant-section-image'))) {
            return asset($this->getFirstMediaUrl('frontend-restaurant-section-image'));
        }
        return asset('images/default/frontend/frontend-restaurant-section-image.png');
    }

    public function getDeliveryImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('frontend-delivery-section-image'))) {
            return asset($this->getFirstMediaUrl('frontend-delivery-section-image'));
        }
        return asset('images/default/frontend/frontend-delivery-section-image.png');
    }
}
