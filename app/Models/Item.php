<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\HasModelMeta;
use App\Traits\Translatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Enums\CropPosition;
use App\Models\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Item extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    use HasModelMeta;
    use Translatable;

    protected $table = "items";
    protected $fillable = [
        'restaurant_id',
        'item_category_id',
        'tax_id',
        'name',
        'slug',
        'caution',
        'description',
        'price',
        'status',
        'item_type',
        'order',
        'is_halal',
        'available_time_start',
        'available_time_end',
        'discount_type',
        'discount',
        'maximum_purchase_quantity',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id'
    ];
    protected array $dates = ['deleted_at'];
    protected $casts = [
        'id'                        => 'integer',
        'restaurant_id'             => 'integer',
        'item_category_id'          => 'integer',
        'tax_id'                    => 'integer',
        'name'                      => 'string',
        'slug'                      => 'string',
        'caution'                   => 'string',
        'description'               => 'string',
        'price'                     => 'decimal:6',
        'status'                    => 'integer',
        'item_type'                 => 'integer',
        'order'                     => 'integer',
        'is_halal'                  => 'integer',
        'available_time_start'      => 'string',
        'available_time_end'        => 'string',
        'discount_type'             => 'integer',
        'discount'                  => 'decimal:6',
        'maximum_purchase_quantity' => 'integer',
        'creator_type'              => 'string',
        'creator_id'                => 'integer',
        'editor_type'               => 'string',
        'editor_id'                 => 'integer'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function getNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslation('description');
    }

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('item'))) {
            $item = $this->getMedia('item')->last();
            return $item->getUrl('thumb');
        }
        return asset('images/default/item/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('item'))) {
            $item = $this->getMedia('item')->last();
            return $item->getUrl('cover');
        }
        return asset('images/default/item/cover.png');
    }

    public function getPreviewAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('item'))) {
            $item = $this->getMedia('item')->last();
            return $item->getUrl('preview');
        }
        return asset('images/default/item/preview.png');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->crop(112, 120, CropPosition::Center)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('cover')->crop(260, 180, CropPosition::Center)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('preview')->width(400)->keepOriginalImageFormat()->sharpen(10);
    }

    public function variations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemVariation::class)->with('itemAttribute')->where(['status' => Status::ACTIVE]);
    }

    public function extras(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemExtra::class)->where(['status' => Status::ACTIVE]);
    }

    public function addons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemAddon::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', 'id');
    }

    public function tax(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'item_id', 'id');
    }
}
