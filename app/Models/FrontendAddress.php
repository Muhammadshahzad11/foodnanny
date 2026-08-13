<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontendAddress extends Model
{
    use HasFactory;

    protected $table = "addresses";
    protected $fillable    = ['label', 'address', 'user_id', 'zone_id', 'apartment', 'latitude', 'longitude'];
    protected $casts = [
        'id'        => 'integer',
        'label'     => 'string',
        'address'   => 'string',
        'user_id'   => 'integer',
        'zone_id'   => 'integer',
        'apartment' => 'string',
        'latitude'  => 'string',
        'longitude' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
