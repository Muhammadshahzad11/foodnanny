<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    protected $table = 'ai_usage_logs';

    protected $fillable = [
        'restaurant_id',
        'total_text_generated_count',
        'total_image_generated_count'
    ];

    protected $casts = [
        'id'                          => 'integer',
        'restaurant_id'               => 'integer',
        'total_text_generated_count'  => 'integer',
        'total_image_generated_count' => 'integer'
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
