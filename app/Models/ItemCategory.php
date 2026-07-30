<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\HasModelMeta;
use App\Traits\Translatable;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ItemCategory extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasModelMeta;
    use Translatable;

    protected $table = "item_categories";
    protected $fillable = ['restaurant_id', 'name', 'slug', 'description', 'status', 'sort', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'name'          => 'string',
        'slug'          => 'string',
        'description'   => 'string',
        'status'        => 'integer',
        'sort'          => 'integer',
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
        if (!empty($this->getFirstMediaUrl('item-category'))) {
            $category = $this->getMedia('item-category')->last();
            return $category->getUrl('thumb');
        }
        return asset('images/default/item-category/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('item-category'))) {
            $category = $this->getMedia('item-category')->last();
            return $category->getUrl('cover');
        }
        return asset('images/default/item-category/cover.png');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(75)->height(48)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('cover')->width(400)->keepOriginalImageFormat()->sharpen(10);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Item::class)->where(['status' => Status::ACTIVE]);
    }
}
