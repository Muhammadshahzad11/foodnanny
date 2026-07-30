<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "analytics";
    protected $fillable = ['name', 'status', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'name'         => 'string',
        'status'       => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function analyticSections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnalyticSection::class, 'analytic_id', 'id');
    }
}
