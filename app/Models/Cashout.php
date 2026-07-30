<?php

namespace App\Models;


use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cashout extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;
    use HasModelMeta;

    protected $table = "cashouts";
    protected $fillable = ['user_id', 'amount', 'date', 'transaction_id', 'remarks', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'             => 'integer',
        'user_id'        => 'integer',
        'amount'         => 'decimal:6',
        'date'           => 'datetime',
        'transaction_id' => 'string',
        'remarks'        => 'string',
        'creator_type'   => 'string',
        'creator_id'     => 'integer',
        'editor_type'    => 'string',
        'editor_id'      => 'integer'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getFileAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('cashout'))) {
            $attachment = $this->getMedia('cashout')->first();
            return $attachment->getUrl();
        }
        return '';
    }
}
