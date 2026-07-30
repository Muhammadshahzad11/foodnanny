<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Statement extends Model
{
    use HasFactory;

    protected $table = "statements";
    protected $fillable = ['model_type', 'model_id', 'date', 'order_id', 'type', 'detail', 'sign', 'amount', 'info'];
    protected $casts = [
        'id'         => 'integer',
        'model_type' => 'string',
        'model_id'   => 'integer',
        'date'       => 'datetime',
        'order_id'   => 'integer',
        'type'       => 'integer',
        'detail'     => 'integer',
        'sign'       => 'string',
        'amount'     => 'decimal:6',
        'info'       => 'string'
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
