<?php

namespace App\Models;

use App\Traits\Translatable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FrontendCoupon extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Translatable;

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
        'limit_per_user'
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
        'limit_per_user'   => 'integer'
    ];

    /**
     * Translations are written by the admin against App\Models\Coupon (same `coupons`
     * row). FrontendCoupon shares the table, so it reads the SAME rows by overriding
     * the morph type to Coupon::class.
     */
    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id', 'id')
            ->where('translatable_type', Coupon::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslation('description');
    }
}
