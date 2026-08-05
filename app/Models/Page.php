<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasModelMeta;

    protected $table = "pages";
    protected $fillable = ['title', 'slug', 'description', 'menu_section_id', 'template_id', 'status', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'              => 'integer',
        'title'           => 'string',
        'slug'            => 'string',
        'description'     => 'string',
        'menu_section_id' => 'integer',
        'template_id'     => 'integer',
        'status'          => 'integer',
        'creator_type'    => 'string',
        'creator_id'      => 'integer',
        'editor_type'     => 'string',
        'editor_id'       => 'integer'
    ];

    public function getImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('page-image'))) {
            return asset($this->getFirstMediaUrl('page-image'));
        }
        return '';
    }

    public function menuSection(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenuSection::class, 'menu_section_id', 'id');
    }
}
