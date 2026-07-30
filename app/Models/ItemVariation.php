<?php

namespace App\Models;

use App\Libraries\AppLibrary;
use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemVariation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasModelMeta;

    protected $table = "item_variations";
    protected $appends = ['convert_price', 'currency_price', 'flat_price'];

    protected $fillable = [
        'restaurant_id',
        'item_id',
        'item_attribute_id',
        'name',
        'price',
        'caution',
        'status',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id'
    ];
    protected $casts = [
        'id'                => 'integer',
        'restaurant_id'     => 'integer',
        'item_id'           => 'integer',
        'item_attribute_id' => 'integer',
        'name'              => 'string',
        'price'             => 'decimal:6',
        'caution'           => 'string',
        'status'            => 'integer',
        'creator_type'      => 'string',
        'creator_id'        => 'integer',
        'editor_type'       => 'string',
        'editor_id'         => 'integer'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function itemAttribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ItemAttribute::class);
    }

    public function getCurrencyPriceAttribute(): string
    {
        return AppLibrary::currencyAmountFormat($this->price);
    }

    public function getFlatPriceAttribute(): string
    {
        return AppLibrary::currencyAmountFormat($this->price);
    }

    public function getConvertPriceAttribute(): float
    {
        return AppLibrary::convertAmountFormat($this->price);
    }
}
