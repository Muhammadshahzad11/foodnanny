<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OneTimePassword extends Model
{
    protected $table = "one_time_passwords";
    protected $fillable = ['provider', 'code', 'token', 'created_at'];
    protected $casts = [
        'provider'   => 'string',
        'code'       => 'string',
        'token'      => 'string',
        'created_at' => 'datetime',
    ];
    public $timestamps = false;
}
