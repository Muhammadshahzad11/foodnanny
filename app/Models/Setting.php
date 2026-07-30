<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $table = "settings";
    protected $fillable = ['group', 'key', 'payload', 'settingable_type', 'settingable_id'];
    protected $casts = [
        'id'               => 'integer',
        'group'            => 'string',
        'key'              => 'json',
        'payload'          => 'string',
        'settingable_type' => 'string',
        'settingable_id'   => 'integer',
    ];
}
