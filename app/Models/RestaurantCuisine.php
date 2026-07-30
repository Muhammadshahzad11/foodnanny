<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantCuisine extends Model
{
    protected $table = "restaurant_cuisines";
    protected $fillable = ['restaurant_id', 'cuisine_id'];

    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'cuisine_id'    => 'integer',
    ];

    public function cuisine(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cuisine::class);
    }
}
