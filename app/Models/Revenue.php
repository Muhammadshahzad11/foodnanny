<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Revenue extends Model
{
    use HasFactory;

    protected $table = "revenues";
    protected $fillable = ['order_id', 'type', 'detail', 'sign', 'order_amount',  'revenue_amount', 'date', 'info'];
    protected $casts = [
        'id'             => 'integer',
        'order_id'       => 'integer',
        'type'           => 'integer',
        'detail'         => 'integer',
        'sign'           => 'string',
        'order_amount'   => 'decimal:6',
        'revenue_amount' => 'decimal:6',
        'date'           => 'datetime',
        'info'           => 'string'
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
