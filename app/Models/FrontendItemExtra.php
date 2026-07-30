<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FrontendItemExtra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "item_extras";
    protected $fillable = ['restaurant_id', 'item_id', 'name', 'status', 'price'];
    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'item_id'       => 'integer',
        'name'          => 'string',
        'status'        => 'integer',
        'price'         => 'decimal:6',
    ];

    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FrontendItem::class, 'item_id', 'id');
    }
}
