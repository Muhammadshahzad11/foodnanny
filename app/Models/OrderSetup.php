<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSetup extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "order_setups";
    protected $fillable = ['restaurant_id', 'food_preparation_time', 'schedule_order_slot_duration', 'minimum_order_limit', 'takeaway', 'delivery', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'                           => 'string',
        'restaurant_id'                => 'integer',
        'food_preparation_time'        => 'integer',
        'schedule_order_slot_duration' => 'integer',
        'minimum_order_limit'          => 'decimal:6',
        'takeaway'                     => 'integer',
        'delivery'                     => 'integer', 
        'creator_type'                 => 'string',
        'creator_id'                   => 'integer',
        'editor_type'                  => 'string',
        'editor_id'                    => 'integer'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }
}
