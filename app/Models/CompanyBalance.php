<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBalance extends Model
{
    protected $table = "company_balances";
    protected $fillable = ['balance'];
    protected $casts = [
        'id'      => 'integer',
        'balance' => 'decimal:6'
    ];
}
