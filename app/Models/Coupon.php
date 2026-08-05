<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;
    use HasModelMeta;
    use Translatable;

    protected $table = "coupons";

    protected $fillable = [
        'name',
        'description',
        'code',
        'restaurant_id',
        'start_date',
        'end_date',
        'discount',
        'discount_type',
        'minimum_order',
        'maximum_discount',
        'limit_per_user',
        'type',
        'owner',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id'
    ];
    protected $casts = [
        'id'               => 'integer',
        'name'             => 'string',
        'description'      => 'string',
        'code'             => 'string',
        'restaurant_id'    => 'integer',
        'start_date'       => 'datetime',
        'end_date'         => 'datetime',
        'discount'         => 'decimal:6',
        'discount_type'    => 'integer',
        'minimum_order'    => 'decimal:6',
        'maximum_discount' => 'decimal:6',
        'limit_per_user'   => 'integer',
        'type'             => 'integer',
        'owner'            => 'integer',
        'creator_type'     => 'string',
        'creator_id'       => 'integer',
        'editor_type'      => 'string',
        'editor_id'        => 'integer'
    ];

    public function getNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslation('description');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }
}
