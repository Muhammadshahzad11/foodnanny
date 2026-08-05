<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Language extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasModelMeta;

    protected $table = "languages";
    protected $fillable = ['name', 'code', 'display_mode', 'status', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'name'         => 'string',
        'code'         => 'string',
        'display_mode' => 'integer',
        'status'       => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function getImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('language'))) {
            return asset($this->getFirstMediaUrl('language'));
        }
        return asset('images/default/language/language.png');
    }
}
