<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfferRestaurant extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "offer_restaurants";
    protected $fillable = ['restaurant_id', 'offer_id', 'apply', 'status', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'offer_id'      => 'integer',
        'apply'         => 'integer',
        'status'        => 'integer',
        'creator_type'  => 'string',
        'creator_id'    => 'integer',
        'editor_type'   => 'string',
        'editor_id'     => 'integer'
    ];

    public function offer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(offer::class, 'offer_id', 'id');
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }
}
