<?php

namespace App\Models;

use App\Models\Scopes\ZoneScope;
use App\Traits\HasModelMeta;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Netsells\GeoScope\Traits\GeoScopeTrait;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model implements HasMedia
{

    use HasFactory;
    use InteractsWithMedia;
    use GeoScopeTrait;
    use HasModelMeta;

    protected $table = "restaurants";
    protected $fillable = ['name', 'slug', 'email', 'country_code', 'phone', 'latitude', 'longitude', 'user_id', 'zone_id', 'city', 'state', 'zip_code', 'address', 'status', 'current_status', 'apply', 'balance', 'online_commission', 'pos_commission', 'terms_and_conditions', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'                   => 'integer',
        'name'                 => 'string',
        'slug'                 => 'string',
        'email'                => 'string',
        'country_code'         => 'string',
        'phone'                => 'string',
        'latitude'             => 'string',
        'longitude'            => 'string',
        'user_id'              => 'integer',
        'zone_id'              => 'integer',
        'city'                 => 'string',
        'state'                => 'string',
        'zip_code'             => 'string',
        'address'              => 'string',
        'status'               => 'integer',
        'current_status'       => 'integer',
        'apply'                => 'integer',
        'balance'              => 'decimal:6',
        'online_commission'    => 'decimal:6',
        'pos_commission'       => 'decimal:6',
        'terms_and_conditions' => 'integer',
        'creator_type'         => 'string',
        'creator_id'           => 'integer',
        'editor_type'          => 'string',
        'editor_id'            => 'integer'
    ];

    public function getImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('restaurant'))) {
            $image = $this->getMedia('restaurant')->last();
            return $image->getUrl();
        }
        return asset('images/default/restaurant/restaurant.png');
    }

    public function getLogoAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('restaurant-logo'))) {
            $logo = $this->getMedia('restaurant-logo')->last();
            return $logo->getUrl();
        }
        return asset('images/default/restaurant/logo.png');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new ZoneScope());
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function zone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Zone::class)->withoutGlobalScopes();
    }

    public function cuisines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RestaurantCuisine::class, 'restaurant_id', 'id');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FrontendItem::class, 'restaurant_id', 'id');
    }

    public function cuisinesWithCuisineRelation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RestaurantCuisine::class, 'restaurant_id', 'id')->with('cuisine');
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Review::class, 'model')->latest();
    }

    public function orderSetup(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FrontendOrderSetup::class, 'restaurant_id', 'id');
    }

    public function deliveryZones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RestaurantDeliveryZone::class, 'restaurant_id', 'id')
            ->withoutGlobalScopes();
    }

    public function activeDeliveryZones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RestaurantDeliveryZone::class, 'restaurant_id', 'id')
            ->withoutGlobalScopes()
            ->where('status', \App\Enums\Status::ACTIVE)
            ->orderBy('id');
    }

    public function timeSlots(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FrontendTimeSlot::class, 'restaurant_id', 'id');
    }

    public function favorite(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Favorite::class);
    }

    public function scopeWithReviewRating($query)
    {
        $reviewsStar      = Review::selectRaw('sum(star)')->where('model_type', Restaurant::class)->whereColumn('model_id', 'restaurants.id')->getQuery();
        $reviewsStarCount = Review::selectRaw('count(model_id)')->where('model_type', Restaurant::class)->whereColumn('model_id', 'restaurants.id')->getQuery();
        $base             = $query->getQuery();
        if (is_null($base->columns)) {
            $query->select([$base->from . '.*']);
        }
        return $query->selectSub($reviewsStar, 'rating_star')->selectSub($reviewsStarCount, 'rating_star_count');
    }

    public function scopeWithDistance($query, $request)
    {
        if ($request->latitude && $request->longitude) {
            return $query->addDistanceFromField($request->latitude, $request->longitude, 'distance');
        }
        return null;
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'restaurant_id', 'id');
    }
}
