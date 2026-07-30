<?php

namespace App\Models;

use App\Models\User;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collection extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "collections";
    protected $fillable = ['source_user_id', 'destination_user_id', 'amount', 'date', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'                  => 'integer',
        'source_user_id'      => 'integer',
        'destination_user_id' => 'integer',
        'amount'              => 'decimal:6',
        'date'                => 'datetime',
        'creator_type'        => 'string',
        'creator_id'          => 'integer',
        'editor_type'         => 'string',
        'editor_id'           => 'integer'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'source_user_id', 'id');
    }

    public function destinationUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'destination_user_id', 'id');
    }
}
