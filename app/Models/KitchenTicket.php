<?php

namespace App\Models;

use App\Models\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenTicket extends Model
{
    protected $table = 'kitchen_tickets';

    protected $fillable = [
        'restaurant_id',
        'order_id',
        'kitchen_station_id',
        'printer_id',
        'ticket_no',
        'print_count',
        'printed_at',
        'printed_by',
        'payload',
    ];

    protected $casts = [
        'id'                 => 'integer',
        'restaurant_id'      => 'integer',
        'order_id'           => 'integer',
        'kitchen_station_id' => 'integer',
        'printer_id'         => 'integer',
        'print_count'        => 'integer',
        'printed_at'         => 'datetime',
        'printed_by'         => 'integer',
        'payload'            => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function kitchenStation(): BelongsTo
    {
        return $this->belongsTo(KitchenStation::class, 'kitchen_station_id');
    }

    public function assignedPrinter(): BelongsTo
    {
        return $this->belongsTo(Printer::class, 'printer_id');
    }

    /** @deprecated Use printedByUser(); kept for older callers expecting "printer" as printed_by user. */
    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    public function printedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }
}
