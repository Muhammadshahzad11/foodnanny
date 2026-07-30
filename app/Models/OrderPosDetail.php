<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPosDetail extends Model
{

    use HasFactory;

    protected $table = "order_pos_details";
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_note',
        'received_amount'
    ];
    protected $casts = [
        'id'              => 'integer',
        'order_id'        => 'integer',
        'payment_method'  => 'integer',
        'payment_note'    => 'string',
        'received_amount' => 'decimal:6',
    ];
}
