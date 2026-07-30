<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $table = "messages";
    protected $fillable = ['order_id', 'user_id', 'text', 'is_read', 'channel_type'];
    protected $casts = [
        'id'           => 'integer',
        'order_id'     => 'integer',
        'user_id'      => 'integer',
        'text'         => 'string',
        'is_read'      => 'integer',
        'channel_type' => 'integer',
        'created_at'   => 'datetime'
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
