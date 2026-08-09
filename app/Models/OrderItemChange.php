<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemChange extends Model
{
    protected $table = 'order_item_changes';

    protected $fillable = [
        'restaurant_id',
        'order_id',
        'order_item_id',
        'item_id',
        'item_name',
        'action',
        'previous_quantity',
        'new_quantity',
        'difference',
        'kot_printed',
        'kitchen_ticket_id',
        'user_id',
        'note',
        'meta',
    ];

    protected $casts = [
        'id'                => 'integer',
        'restaurant_id'     => 'integer',
        'order_id'          => 'integer',
        'order_item_id'     => 'integer',
        'item_id'           => 'integer',
        'previous_quantity' => 'float',
        'new_quantity'      => 'float',
        'difference'        => 'float',
        'kot_printed'       => 'boolean',
        'kitchen_ticket_id' => 'integer',
        'user_id'           => 'integer',
        'meta'              => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
