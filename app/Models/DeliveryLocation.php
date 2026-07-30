<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryLocation extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "delivery_locations";
    protected $fillable = ['user_id', 'latitude', 'longitude', 'address', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'user_id'      => 'integer',
        'latitude'     => 'string',
        'longitude'    => 'string',
        'address'      => 'string',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
