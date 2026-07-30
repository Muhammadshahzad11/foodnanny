<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Cuisine extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasModelMeta;

    protected $table = "cuisines";
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'sort',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id'
    ];

    protected $casts = [
        'id'           => 'integer',
        'name'         => 'string',
        'slug'         => 'string',
        'description'  => 'string',
        'status'       => 'integer',
        'sort'         => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function getImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('cuisine'))) {
            return asset($this->getFirstMediaUrl('cuisine'));
        }
        return asset('images/default/cuisine/cuisine.png');
    }
}
