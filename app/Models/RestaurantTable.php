<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantTable extends Model
{
    use HasFactory;
    use HasModelMeta;
    use SoftDeletes;

    protected $table = 'restaurant_tables';

    protected $fillable = [
        'restaurant_id',
        'table_number',
        'name',
        'capacity',
        'zone',
        'status',
        'notes',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id',
    ];

    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'table_number'  => 'string',
        'name'          => 'string',
        'capacity'      => 'integer',
        'zone'          => 'string',
        'status'        => 'integer',
        'notes'         => 'string',
        'creator_type'  => 'string',
        'creator_id'    => 'integer',
        'editor_type'   => 'string',
        'editor_id'     => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function creator(): MorphTo
    {
        return $this->morphTo();
    }

    public function editor(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Future dine-in orders (requires orders.table_id).
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    /**
     * Reserved for Module 3+ dining sessions.
     * Intentionally returns a constrained empty relation until DiningSession exists.
     */
    public function diningSessions(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id')->whereRaw('1 = 0');
    }
}
