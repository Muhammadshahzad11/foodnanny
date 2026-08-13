<?php

namespace App\Models;

use App\Enums\Status;
use App\Models\Scopes\ZoneScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory;
    use HasModelMeta;
    use SoftDeletes;

    protected $table = 'zones';

    protected $fillable = [
        'name',
        'display_name',
        'polygon',
        'status',
        'base_delivery_fee',
        'min_order_amount',
        'free_delivery_above',
        'free_delivery_km',
        'extra_distance_charge',
        'peak_enabled',
        'peak_charge',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id',
    ];

    protected $casts = [
        'id'                    => 'integer',
        'name'                  => 'string',
        'display_name'          => 'string',
        'polygon'               => 'array',
        'status'                => 'integer',
        'base_delivery_fee'     => 'decimal:6',
        'min_order_amount'      => 'decimal:6',
        'free_delivery_above'   => 'decimal:6',
        'free_delivery_km'      => 'decimal:6',
        'extra_distance_charge' => 'decimal:6',
        'peak_enabled'          => 'integer',
        'peak_charge'           => 'decimal:6',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new ZoneScope());
    }

    public function restaurants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function admins(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'zone_id')->withoutGlobalScopes();
    }

    public function deliveryBoys(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'zone_id')->withoutGlobalScopes();
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE);
    }
}
