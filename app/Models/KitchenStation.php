<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenStation extends Model
{
    protected $table = 'kitchen_stations';

    protected $fillable = [
        'restaurant_id',
        'name',
        'code',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'sort_order'    => 'integer',
        'status'        => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'kitchen_station_id');
    }
}
