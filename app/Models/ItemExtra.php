<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemExtra extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasModelMeta;

    protected $table = "item_extras";
    protected $fillable = ['restaurant_id', 'item_id', 'name', 'status', 'price', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'item_id'       => 'integer',
        'name'          => 'string',
        'status'        => 'integer',
        'price'         => 'decimal:6',
        'creator_type'  => 'string',
        'creator_id'    => 'integer',
        'editor_type'   => 'string',
        'editor_id'     => 'integer'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
