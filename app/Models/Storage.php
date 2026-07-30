<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $table = "storages";
    protected $fillable = ['name', 'slug', 'misc', 'status'];
    protected $casts = [
        'id'     => 'integer',
        'name'   => 'string',
        'slug'   => 'string',
        'misc'   => 'string',
        'status' => 'integer'
    ];

    public function awsOptions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(GatewayOption::class, 'model');
    }
}
