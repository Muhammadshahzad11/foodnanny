<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = "order_items";
    protected $fillable = [
        'order_id',
        'restaurant_id',
        'item_id',
        'quantity',
        'discount',
        'tax_name',
        'tax_rate',
        'tax_type',
        'tax_amount',
        'price',
        'item_variations',
        'item_extras',
        'item_variation_total',
        'item_extra_total',
        'total_price',
        'instruction',
        'status'
    ];
    protected $casts = [
        'id'                   => 'integer',
        'order_id'             => 'integer',
        'restaurant_id'        => 'integer',
        'item_id'              => 'integer',
        'quantity'             => 'integer',
        'discount'             => 'decimal:6',
        'tax_name'             => 'string',
        'tax_rate'             => 'string',
        'tax_type'             => 'integer',
        'tax_amount'           => 'decimal:6',
        'price'                => 'decimal:6',
        'item_variations'      => 'string',
        'item_extras'          => 'string',
        'item_variation_total' => 'decimal:6',
        'item_extra_total'     => 'decimal:6',
        'total_price'          => 'decimal:6',
        'instruction'          => 'string',
        'status'               => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    
    public function orderItem()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')->withTrashed();
    }
}
