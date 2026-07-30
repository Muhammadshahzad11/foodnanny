<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemAttribute extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "item_attributes";
    protected $fillable = ['restaurant_id', 'name', 'status', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'name'          => 'string',
        'status'        => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }
}
