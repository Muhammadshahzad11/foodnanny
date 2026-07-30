<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Scopes\RestaurantScope;
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


    protected $table = "orders";
    protected $fillable = [
        'order_serial_no',
        'token',
        'user_id',
        'restaurant_id',
        'subtotal',
        'discount',
        'delivery_fee',
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
        'delivery_boy_request',
        'service_fee',
        'reason',
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
        'subtotal'             => 'decimal:6',
        'discount'             => 'decimal:6',
        'delivery_fee'         => 'decimal:6',
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
        'is_received'          => 'integer',
        'delivery_boy_request' => 'integer',
        'service_fee'          => 'decimal:6',
        'reason'               => 'string',
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
