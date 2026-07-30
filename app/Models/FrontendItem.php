<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\Translatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Enums\CropPosition;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FrontendItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes, Translatable;

    protected $table = "items";
    protected $fillable = [
        'restaurant_id',
        'name',
        'item_category_id',
        'slug',
        'tax_id',
        'item_type',
        'price',
        'description',
        'caution',
        'status',
        'order',
    ];
    protected array $dates = ['deleted_at'];
    protected $casts = [
        'id'               => 'integer',
        'restaurant_id'    => 'integer',
        'name'             => 'string',
        'item_category_id' => 'integer',
        'slug'             => 'string',
        'tax_id'           => 'integer',
        'item_type'        => 'integer',
        'price'            => 'decimal:6',
        'description'      => 'string',
        'caution'          => 'string',
        'status'           => 'integer',
        'order'            => 'integer',
    ];

    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id', 'id')->where('translatable_type', Item::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslation('description');
    }

    public function getThumbAttribute()
    {
        if (!empty($this->getFirstMediaUrl('frontend-item'))) {
            $item = $this->getMedia('frontend-item')->last();
            return $item->getUrl('thumb');
        }
        return asset('images/default/item/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('frontend-item'))) {
            $item = $this->getMedia('frontend-item')->last();
            return $item->getUrl('cover');
        }
        return asset('images/default/item/cover.png');
    }

    public function getPreviewAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('frontend-item'))) {
            $item = $this->getMedia('frontend-item')->last();
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
        return $this->hasMany(FrontendItemVariation::class, 'item_id', 'id')->where(['status' => Status::ACTIVE]);
    }

    public function extras(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FrontendItemExtra::class, 'item_id', 'id')->where(['status' => Status::ACTIVE]);
    }

    public function addons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FrontendItemAddon::class, 'item_id', 'id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FrontendItemCategory::class, 'item_category_id', 'id');
    }

    public function tax(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FrontendTax::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'item_id', 'id');
    }
}
