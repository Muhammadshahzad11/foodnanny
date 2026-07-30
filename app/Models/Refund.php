<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "refunds";
    protected $fillable = ['order_serial_no', 'refund_amount', 'deduction_amount', 'responsible_type', 'responsible_id', 'info', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'               => 'integer',
        'order_serial_no'  => 'string',
        'refund_amount'    => 'decimal:6',
        'deduction_amount' => 'decimal:6',
        'responsible_type' => 'string',
        'responsible_id'   => 'integer',
        'info'             => 'string',
        'creator_type'     => 'string',
        'creator_id'       => 'integer',
        'editor_type'      => 'string',
        'editor_id'        => 'integer'
    ];
}
