<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignRestaurant extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "campaign_restaurants";
    protected $fillable = ['campaign_id', 'restaurant_id', 'apply', 'status', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'            => 'integer',
        'campaign_id'   => 'integer',
        'restaurant_id' => 'integer',
        'apply'         => 'integer',
        'status'        => 'integer',
        'creator_type'  => 'string',
        'creator_id'    => 'integer',
        'editor_type'   => 'string',
        'editor_id'     => 'integer'
    ];

    public function campaign(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }
}
