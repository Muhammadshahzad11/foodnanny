<?php

namespace App\Models;

use App\Enums\Status;
use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantDeliveryZone extends Model
{
    use HasFactory;
    use HasModelMeta;
    use SoftDeletes;

    protected $table = 'restaurant_delivery_zones';

    protected $fillable = [
        'restaurant_id',
        'name',
        'display_name',
        'polygon',
        'status',
        'charge_type',
        'min_delivery_charge',
        'max_delivery_charge',
        'charge_per_km',
        'max_cod_amount',
        'additional_delivery_charge',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id',
    ];

    protected $casts = [
        'id'                          => 'integer',
        'restaurant_id'               => 'integer',
        'name'                        => 'string',
        'display_name'                => 'string',
        'polygon'                     => 'array',
        'status'                      => 'integer',
        'charge_type'                 => 'integer',
        'min_delivery_charge'         => 'decimal:6',
        'max_delivery_charge'         => 'decimal:6',
        'charge_per_km'               => 'decimal:6',
        'max_cod_amount'              => 'decimal:6',
        'additional_delivery_charge'  => 'decimal:6',
        'creator_type'                => 'string',
        'creator_id'                  => 'integer',
        'editor_type'                 => 'string',
        'editor_id'                   => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE);
    }
}
