<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Pwa extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'pwas';

    protected $fillable = [
        'id',
        'name',
        'short_name',
        'description',
        'theme_color',
        'background_color',
        'orientation',
        'display_mode',
        'offline_mode',
        'auto_update',
        'cache_strategy',
        'enable_install_popup',
        'popup_delay_seconds',
        'popup_frequency_hours',
        'cache_version',
        'force_updated_at',
    ];

    protected $casts = [
        'offline_mode' => 'boolean',
        'auto_update' => 'boolean',
        'enable_install_popup' => 'boolean',
        'popup_delay_seconds' => 'integer',
        'popup_frequency_hours' => 'integer',
        'cache_version' => 'integer',
        'force_updated_at' => 'datetime',
    ];

    public function getSplashAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('pwa_splash'))) {
            $pwa = $this->getMedia('pwa_splash')->first();
            return $pwa->getUrl('D_2048x2732');
        }
        return asset('images/default/pwa/splashes/splash-2048x2732.png');
    }

    public function getIconAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('pwa_icon'))) {
            $pwa = $this->getMedia('pwa_icon')->first();
            return $pwa->getUrl('D_512x512');
        }
        return asset('images/default/pwa/icons/icon-512x512.png');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Process immediately so admin Save shows updated icons without a queue worker
        $this->addMediaConversion('D_2048x2732')->performOnCollections('pwa_splash')->nonQueued()->width(2048)->height(2732)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_1668x2388')->performOnCollections('pwa_splash')->nonQueued()->width(1668)->height(2388)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_1668x2224')->performOnCollections('pwa_splash')->nonQueued()->width(1668)->height(2224)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_1536x2048')->performOnCollections('pwa_splash')->nonQueued()->width(1536)->height(2048)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_1242x2688')->performOnCollections('pwa_splash')->nonQueued()->width(1242)->height(2688)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_1242x2208')->performOnCollections('pwa_splash')->nonQueued()->width(1242)->height(2208)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_1125x2436')->performOnCollections('pwa_splash')->nonQueued()->width(1125)->height(2436)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_828x1792')->performOnCollections('pwa_splash')->nonQueued()->width(828)->height(1792)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_750x1334')->performOnCollections('pwa_splash')->nonQueued()->width(750)->height(1334)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_640x1136')->performOnCollections('pwa_splash')->nonQueued()->width(640)->height(1136)->keepOriginalImageFormat()->sharpen(10);

        $this->addMediaConversion('D_512x512')->performOnCollections('pwa_icon')->nonQueued()->width(512)->height(512)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_384x384')->performOnCollections('pwa_icon')->nonQueued()->width(384)->height(384)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_192x192')->performOnCollections('pwa_icon')->nonQueued()->width(192)->height(192)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_152x152')->performOnCollections('pwa_icon')->nonQueued()->width(152)->height(152)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_144x144')->performOnCollections('pwa_icon')->nonQueued()->width(144)->height(144)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_128x128')->performOnCollections('pwa_icon')->nonQueued()->width(128)->height(128)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_96x96')->performOnCollections('pwa_icon')->nonQueued()->width(96)->height(96)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('D_72x72')->performOnCollections('pwa_icon')->nonQueued()->width(72)->height(72)->keepOriginalImageFormat()->sharpen(10);
    }
}
