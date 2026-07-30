<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payout extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "payouts";
    protected $fillable = ['model_type', 'model_id', 'amount', 'date', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'         => 'integer',
        'model_type' => 'string',
        'model_id'   => 'integer',
        'amount'     => 'decimal:6',
        'date'       => 'datetime',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function model(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
