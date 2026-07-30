<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AboutStep extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasModelMeta;

    protected $table = "about_steps";
    protected $fillable = ['title', 'description', 'status', 'sort', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'title'        => 'string',
        'description'  => 'string',
        'status'       => 'integer',
        'sort'         => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('about-steps'))) {
            $aboutStep = $this->getMedia('about-steps')->last();
            return $aboutStep->getUrl('thumb');
        }
        return asset('images/default/about-steps/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('about-steps'))) {
            $aboutStep = $this->getMedia('about-steps')->last();
            return $aboutStep->getUrl('cover');
        }
        return asset('images/default/about-steps/cover.png');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(60)->height(60)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('cover')->width(400)->keepOriginalImageFormat()->sharpen(10);
    }
}
