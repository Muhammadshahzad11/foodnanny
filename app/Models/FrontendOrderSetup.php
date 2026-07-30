<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontendOrderSetup extends Model
{
    use HasFactory;

    protected $table = "order_setups";
    protected $fillable = ['restaurant_id', 'food_preparation_time', 'schedule_order_slot_duration', 'minimum_order_limit', 'takeaway', 'delivery'];
    protected $casts = [
        'id'                           => 'string',
        'restaurant_id'                => 'integer',
        'food_preparation_time'        => 'integer',
        'schedule_order_slot_duration' => 'integer',
        'minimum_order_limit'          => 'integer',
        'takeaway'                     => 'integer',
        'delivery'                     => 'integer'
    ];
}
