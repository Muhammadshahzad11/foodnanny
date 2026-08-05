<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Benefit extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasModelMeta;

    protected $table = "benefits";
    protected $fillable = ["title", 'description', 'status', 'sort', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'          => 'integer',
        'title'       => 'string',
        'description' => 'string',
        'status'      => 'integer',
        'sort'        => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('benefit'))) {
            $benefit = $this->getMedia('benefit')->last();
            return $benefit->getUrl('thumb');
        }
        return asset('images/default/benefit/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('benefit'))) {
            $benefit = $this->getMedia('benefit')->last();
            return $benefit->getUrl('cover');
        }
        return asset('images/default/benefit/cover.png');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(32)->height(32)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('cover')->width(400)->keepOriginalImageFormat()->sharpen(10);
    }
}
