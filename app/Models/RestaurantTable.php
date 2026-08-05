<?php

namespace App\Models;

use App\Enums\TableStatus;
use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RestaurantTable extends Model implements HasMedia
{
    use HasFactory;
    use HasModelMeta;
    use InteractsWithMedia;
    use SoftDeletes;

    public const MEDIA_COLLECTION_QR = 'qr-code';

    protected $table = 'restaurant_tables';

    protected $fillable = [
        'restaurant_id',
        'uuid',
        'table_number',
        'name',
        'capacity',
        'zone',
        'status',
        'notes',
        'qr_token',
        'qr_version',
        'qr_generated_at',
        'qr_url',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id',
    ];

    protected $casts = [
        'id'              => 'integer',
        'restaurant_id'   => 'integer',
        'uuid'            => 'string',
        'table_number'    => 'string',
        'name'            => 'string',
        'capacity'        => 'integer',
        'zone'            => 'string',
        'status'          => 'integer',
        'notes'           => 'string',
        'qr_token'        => 'string',
        'qr_version'      => 'integer',
        'qr_generated_at' => 'datetime',
        'qr_url'          => 'string',
        'creator_type'    => 'string',
        'creator_id'      => 'integer',
        'editor_type'     => 'string',
        'editor_id'       => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());

        static::creating(function (RestaurantTable $table) {
            if (empty($table->uuid)) {
                $table->uuid = (string) Str::uuid();
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_QR)->singleFile();
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function creator(): MorphTo
    {
        return $this->morphTo();
    }

    public function editor(): MorphTo
    {
        return $this->morphTo();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    /**
     * Reserved for future DiningSession model.
     */
    public function diningSessions(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id')->whereRaw('1 = 0');
    }

    public function hasQr(): bool
    {
        return filled($this->qr_token);
    }

    public function isQrScannable(): bool
    {
        return $this->hasQr()
            && !in_array((int) $this->status, [TableStatus::INACTIVE, TableStatus::OUT_OF_SERVICE], true);
    }

    public function qrImageUrl(): ?string
    {
        $url = $this->getFirstMediaUrl(self::MEDIA_COLLECTION_QR);

        return $url !== '' ? $url : null;
    }
}
