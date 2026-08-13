<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "addresses";
    protected $fillable = ['label', 'address', 'user_id', 'zone_id', 'apartment', 'latitude', 'longitude', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'label'        => 'string',
        'address'      => 'string',
        'user_id'      => 'integer',
        'zone_id'      => 'integer',
        'apartment'    => 'string',
        'latitude'     => 'string',
        'longitude'    => 'string',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
