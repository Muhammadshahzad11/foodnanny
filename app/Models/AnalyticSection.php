<?php

namespace App\Models;

use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticSection extends Model
{
    use HasFactory;
    use HasModelMeta;

    protected $table = "analytic_sections";
    protected $fillable = ['analytic_id', 'name', 'data', 'section', 'creator_type', 'creator_id', 'editor_type', 'editor_id'];
    protected $casts = [
        'id'           => 'integer',
        'analytic_id'  => 'integer',
        'name'         => 'string',
        'data'         => 'string',
        'section'      => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer'
    ];

    public function analytic(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Analytic::class);
    }
}
