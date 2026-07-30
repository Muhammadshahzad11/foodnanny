<?php

namespace App\Models;

use App\Enums\Status;
use App\Models\Translation;
use App\Traits\Translatable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class FrontendItemCategory extends Model implements HasMedia
{
    use InteractsWithMedia;
    use Translatable;

    protected $table = "item_categories";
    protected $fillable = ['restaurant_id', 'name', 'slug', 'description', 'status'];
    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'name'          => 'string',
        'slug'          => 'string',
        'description'   => 'string',
        'status'        => 'integer',
    ];

    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id', 'id')->where('translatable_type', ItemCategory::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslation('description');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FrontendItem::class, 'item_category_id', 'id')->where(['status' => Status::ACTIVE]);
    }
}
