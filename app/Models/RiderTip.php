<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;

class RiderTip extends Model
{
    use HasModelMeta;

    protected $table = "rider_tips";
    protected $fillable = ['label', 'amount', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];

    protected $casts = [
        'id'           => 'integer',
        'label'        => 'string',
        'amount'       => 'decimal:6',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];
}
