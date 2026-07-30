<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory;
    use HasModelMeta;
    use Translatable;

    protected $table = "coupons";

    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'code',
        'discount',
        'discount_type',
        'start_date',
        'end_date',
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
        'restaurant_id'    => 'integer',
        'name'             => 'string',
        'description'      => 'string',
        'code'             => 'string',
        'discount'         => 'decimal:6',
        'discount_type'    => 'integer',
        'start_date'       => 'datetime',
        'end_date'         => 'datetime',
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
}
