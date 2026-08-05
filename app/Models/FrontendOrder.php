<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FrontendOrder extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "orders";
    protected $fillable = [
        'restaurant_id',
        'table_id',
        'order_serial_no',
        'token',
        'user_id',
        'subtotal',
        'discount',
        'delivery_fee',
        'extra_delivery_fee',
        'total',
        'order_type',
        'order_datetime',
        'delivery_time',
        'preparation_time',
        'is_advance_order',
        'address',
        'payment_method',
        'payment_status',
        'delivery_boy_request',
        'status',
        'source',
        'cutlery',
        'service_fee',
        'rider_tip',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id'
    ];

    protected $casts = [
        'id'                   => 'integer',
        'restaurant_id'        => 'integer',
        'table_id'             => 'integer',
        'order_serial_no'      => 'string',
        'token'                => 'string',
        'user_id'              => 'integer',
        'subtotal'             => 'decimal:6',
        'discount'             => 'decimal:6',
        'delivery_fee'         => 'decimal:6',
        'extra_delivery_fee'   => 'decimal:6',
        'total'                => 'decimal:6',
        'order_type'           => 'integer',
        'order_datetime'       => 'datetime',
        'delivery_time'        => 'string',
        'preparation_time'     => 'integer',
        'is_advance_order'     => 'integer',
        'payment_method'       => 'integer',
        'payment_status'       => 'integer',
        'status'               => 'integer',
        'delivery_boy_request' => 'integer',
        'source'               => 'string',
        'cutlery'              => 'integer',
        'service_fee'          => 'decimal:6',
        'rider_tip'            => 'decimal:6',
        'creator_type'         => 'string',
        'creator_id'           => 'integer',
        'editor_type'          => 'string',
        'editor_id'            => 'integer'
    ];

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FrontendOrderItem::class, 'order_id', 'id');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'order_items');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function address(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id');
    }

    public function deliveryBoy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_boy_id', 'id');
    }

    public function coupon(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderCoupon::class, 'order_id', 'id');
    }

    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::PENDING);
    }

    public function scopePreparing($query)
    {
        return $query->where('status', OrderStatus::PREPARING);
    }

    public function scopeOutForDelivery($query)
    {
        return $query->where('status', OrderStatus::OUT_FOR_DELIVERY);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', OrderStatus::DELIVERED);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', OrderStatus::CANCELED);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', OrderStatus::RETURNED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', OrderStatus::REJECTED);
    }

    public function transaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Transaction::class, 'order_id', 'id');
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Restaurant::class, 'id', 'restaurant_id');
    }

    public function diningTable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }
}
