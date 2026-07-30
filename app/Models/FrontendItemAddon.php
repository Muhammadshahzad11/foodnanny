<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FrontendItemAddon extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "item_addons";
    protected $fillable = ['restaurant_id', 'item_id', 'addon_item_id', 'addon_item_variation'];
    protected $casts = [
        'id'                   => 'integer',
        'restaurant_id'        => 'integer',
        'item_id'              => 'integer',
        'addon_item_id'        => 'integer',
        'addon_item_variation' => 'string',
    ];

    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FrontendItem::class, 'item_id', 'id');
    }
    public function addonItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FrontendItem::class, 'addon_item_id', 'id');
    }
}
