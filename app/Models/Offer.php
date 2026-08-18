<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use App\Traits\Translatable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasModelMeta;
    use Translatable;

    protected $table = "offers";
    protected $fillable = ['title', 'tag', 'slug', 'description', 'location', 'latitude', 'longitude', 'amount', 'status', 'start_date', 'end_date', 'start_time', 'end_time', 'type', 'is_single', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'name'         => 'string',
        'tag'          => 'string',
        'slug'         => 'string',
        'description'  => 'string',
        'location'     => 'string',
        'latitude'     => 'string',
        'longitude'    => 'string',
        'amount'       => 'decimal:6',
        'status'       => 'integer',
        'start_date'   => 'string',
        'end_date'     => 'string',
        'start_time'   => 'string',
        'end_time'     => 'string',
        'type'         => 'integer',
        'is_single'    => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function getTitleAttribute(): ?string
    {
        return $this->getTranslation('title');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslation('description');
    }

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('offer-thumb'))) {
            return asset($this->getFirstMediaUrl('offer-thumb'));
        }
        return asset('images/default/offer/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('offer-cover'))) {
            return asset($this->getFirstMediaUrl('offer-cover'));
        }
        return asset('images/default/offer/cover.png');
    }

    public function restaurants(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'offer_restaurants');
    }

    public function offerRestaurants(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(OfferRestaurant::class, 'offer_id', 'id');
    }
}
