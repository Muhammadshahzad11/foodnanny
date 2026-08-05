<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use App\Traits\Translatable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasModelMeta;
    use Translatable;

    protected $table = "campaigns";
    protected $fillable = ['title', 'slug', 'description', 'amount', 'start_date', 'end_date', 'start_time', 'end_time', 'type', 'status', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'title'        => 'string',
        'slug'         => 'string',
        'description'  => 'string',
        'amount'       => 'decimal:6',
        'start_date'   => 'string',
        'end_date'     => 'string',
        'start_time'   => 'string',
        'end_time'     => 'string',
        'type'         => 'integer',
        'status'       => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function getTitleAttribute(): ?string
    {
        return $this->getTranslation('title');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslation('description');
    }

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('campaign-thumb'))) {
            return asset($this->getFirstMediaUrl('campaign-thumb'));
        }
        return asset('images/default/campaign/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('campaign-cover'))) {
            return asset($this->getFirstMediaUrl('campaign-cover'));
        }
        return asset('images/default/campaign/cover.png');
    }

    public function restaurants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'campaign_restaurants');
    }

    public function campaignRestaurants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignRestaurant::class, 'campaign_id', 'id');
    }
}
