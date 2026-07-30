<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FrontendTax extends Model
{
    use HasFactory;

    protected $table = "taxes";
    protected $fillable = ['restaurant_id', 'name', 'code', 'tax_rate', 'type', 'status'];
    protected $casts = [
        'id'            => 'integer',
        'restaurant_id' => 'integer',
        'name'          => 'string',
        'code'          => 'string',
        'tax_rate'      => 'string',
        'type'          => 'integer',
        'status'        => 'integer',
    ];

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FrontendItem::class)->where(['status' => Status::ACTIVE]);
    }
}
