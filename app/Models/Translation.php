<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends Model
{
    use HasFactory;

    protected $table = "translations";
    protected $fillable = ['translatable_type', 'translatable_id', 'locale', 'key', 'value'];
    protected $casts = [
        'id'                => 'integer',
        'translatable_type' => 'string',
        'translatable_id'   => 'integer',
        'locale'            => 'string',
        'key'               => 'string',
        'value'             => 'string',
    ];

    public function translatable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
