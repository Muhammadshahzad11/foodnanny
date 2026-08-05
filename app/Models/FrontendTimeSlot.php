<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FrontendTimeSlot extends Model
{
    use HasFactory;

    protected $table = "time_slots";
    protected $fillable = ['restaurant_id', 'opening_time', 'closing_time', 'day'];
    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'opening_time'  => 'string',
        'closing_time'  => 'string',
        'day'           => 'integer',
    ];
}
