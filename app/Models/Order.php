<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Scopes\RestaurantScope;
use App\Traits\DetectsScanMenuOrder;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use HasFactory;
    use HasModelMeta;
    use InteractsWithMedia;
    use DetectsScanMenuOrder;


    protected $table = "orders";
    protected $fillable = [
        'order_serial_no',
        'token',
        'user_id',
        'restaurant_id',
        'zone_id',
        'table_id',
        'waiter_id',
        'subtotal',
        'discount',
        'delivery_fee',
        'delivery_zone_id',
        'extra_delivery_fee',
        'total_tax',
        'total',
        'order_type',
        'order_datetime',
        'delivery_time',
        'preparation_time',
        'is_advance_order',
        'payment_method',
        'payment_status',
        'status',
        'delivery_boy_id',
        'is_received',
        'delivery_otp',
        'delivery_otp_verified_at',
        'delivery_boy_request',
        'service_fee',
        'reason',
        'order_note',
        'customer_name',
        'customer_phone',
        'customer_address',
        'delivery_note',
        'billing_requested_at',
        'kitchen_priority',
        'kitchen_station_id',
        'kitchen_accepted_by',
        'kitchen_accepted_at',
        'kitchen_preparing_by',
        'kitchen_ready_by',
        'source',
        'active',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id'
    ];

    protected $casts = [
        'id'                   => 'integer',
        'order_serial_no'      => 'string',
        'token'                => 'string',
        'user_id'              => 'integer',
        'restaurant_id'        => 'integer',
        'zone_id'              => 'integer',
        'table_id'             => 'integer',
        'waiter_id'            => 'integer',
        'subtotal'             => 'decimal:6',
        'discount'             => 'decimal:6',
        'delivery_fee'         => 'decimal:6',
        'delivery_zone_id'     => 'integer',
        'extra_delivery_fee'   => 'decimal:6',
        'total_tax'            => 'decimal:6',
        'total'                => 'decimal:6',
        'order_type'           => 'integer',
        'order_datetime'       => 'datetime',
        'delivery_time'        => 'string',
        'preparation_time'     => 'integer',
        'is_advance_order'     => 'integer',
        'payment_method'       => 'integer',
        'payment_status'       => 'integer',
        'status'               => 'integer',
        'delivery_boy_id'      => 'integer',
        'is_received'              => 'integer',
        'delivery_otp'             => 'string',
        'delivery_otp_verified_at' => 'datetime',
        'delivery_boy_request'     => 'integer',
        'service_fee'          => 'decimal:6',
        'reason'               => 'string',
        'order_note'           => 'string',
        'customer_name'        => 'string',
        'customer_phone'       => 'string',
        'customer_address'     => 'string',
        'delivery_note'        => 'string',
        'billing_requested_at' => 'datetime',
        'kitchen_priority'     => 'integer',
        'kitchen_station_id'   => 'integer',
        'kitchen_accepted_by'  => 'integer',
        'kitchen_accepted_at'  => 'datetime',
        'kitchen_preparing_by' => 'integer',
        'kitchen_ready_by'     => 'integer',
        'source'               => 'integer',
        'active'               => 'integer', 
        'creator_type'         => 'string',
        'creator_id'           => 'integer',
        'editor_type'          => 'string',
        'editor_id'            => 'integer'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'order_items')->withTrashed();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function address(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function diningTable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function waiter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id', 'id')->withTrashed();
    }

    public function kitchenStation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KitchenStation::class, 'kitchen_station_id');
    }

    public function kitchenAcceptedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'kitchen_accepted_by')->withTrashed();
    }

    public function kitchenPreparingBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'kitchen_preparing_by')->withTrashed();
    }

    public function kitchenReadyBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'kitchen_ready_by')->withTrashed();
    }

    public function kitchenTickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KitchenTicket::class);
    }

    public function itemChanges(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItemChange::class);
    }

    public function kitchenStatusLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KitchenStatusLog::class);
    }

    public function deliveryBoy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_boy_id', 'id');
    }

    public function coupon(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderCoupon::class);
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
        return $this->hasOne(Transaction::class);
    }

    public function posDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderPosDetail::class);
    }

    public function getReturnImagesAttribute(): array
    {
        $response = [];
        if (!empty($this->getFirstMediaUrl('return-image'))) {
            $images = $this->getMedia('return-image');
            foreach ($images as $image) {
                $response[] = $image['original_url'];
            }
        }
        return $response;
    }
}
