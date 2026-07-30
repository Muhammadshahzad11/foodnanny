<?php

namespace App\Models;

use App\Libraries\AppLibrary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FrontendItemVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "item_variations";
    protected $appends = ['convert_price', 'currency_price', 'flat_price'];

    protected $fillable = [
        'restaurant_id',
        'item_id',
        'item_attribute_id',
        'name',
        'price',
        'caution',
        'status'
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
    ];

    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FrontendItem::class);
    }

    public function itemAttribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FrontendItemAttribute::class);
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
