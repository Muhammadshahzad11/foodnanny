<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasModelMeta;

    protected $table = "currencies";
    protected $fillable = ['name', 'symbol', 'code', 'is_cryptocurrency', 'exchange_rate', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];

    protected $casts = [
        'id'                => 'integer',
        'name'              => 'string',
        'symbol'            => 'string',
        'code'              => 'string',
        'is_cryptocurrency' => 'integer',
        'exchange_rate'     => 'decimal:6',
        'creator_type'      => 'string',
        'creator_id'        => 'integer',
        'editor_type'       => 'string',
        'editor_id'         => 'integer'
    ];
}
