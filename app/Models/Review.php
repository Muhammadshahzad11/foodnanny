<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasModelMeta;

    protected $table = "reviews";
    protected $fillable = ["user_id", 'model_type', 'model_id', 'star', 'review', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'user_id'      => 'integer',
        'model_type'   => 'string',
        'model_id'     => 'integer',
        'star'         => 'integer',
        'review'       => 'string',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
