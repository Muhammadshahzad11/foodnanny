<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenStatusLog extends Model
{
    protected $table = 'kitchen_status_logs';

    protected $fillable = [
        'restaurant_id',
        'order_id',
        'from_status',
        'to_status',
        'user_id',
        'action',
        'meta',
    ];

    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'order_id'      => 'integer',
        'from_status'   => 'integer',
        'to_status'     => 'integer',
        'user_id'       => 'integer',
        'meta'          => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
