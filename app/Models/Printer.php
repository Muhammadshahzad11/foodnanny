<?php

namespace App\Models;

use App\Enums\Ask;
use App\Enums\PrintFormat;
use App\Enums\PrinterType;
use App\Enums\PrintingChoice;
use App\Enums\Status;
use App\Models\Scopes\RestaurantScope;
use App\Traits\HasModelMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Printer extends Model
{
    use HasModelMeta;

    protected $table = 'printers';

    protected $fillable = [
        'restaurant_id',
        'name',
        'printing_choice',
        'print_format',
        'printer_type',
        'characters_per_line',
        'open_cash_drawer',
        'invoice_qr_status',
        'computer_ipv4',
        'printer_ip',
        'printer_port',
        'windows_printer_name',
        'status',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id',
    ];

    protected $casts = [
        'id'                   => 'integer',
        'restaurant_id'        => 'integer',
        'name'                 => 'string',
        'printing_choice'      => 'integer',
        'print_format'         => 'integer',
        'printer_type'         => 'integer',
        'characters_per_line'  => 'integer',
        'open_cash_drawer'     => 'integer',
        'invoice_qr_status'    => 'integer',
        'computer_ipv4'        => 'string',
        'printer_ip'           => 'string',
        'printer_port'         => 'integer',
        'windows_printer_name' => 'string',
        'status'               => 'integer',
        'creator_type'         => 'string',
        'creator_id'           => 'integer',
        'editor_type'          => 'string',
        'editor_id'            => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new RestaurantScope());
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function kitchens(): HasMany
    {
        return $this->hasMany(KitchenStation::class, 'printer_id');
    }

    public function isDirectPrint(): bool
    {
        return (int) $this->printing_choice === PrintingChoice::DIRECT_PRINT;
    }

    public function isBrowserPopup(): bool
    {
        return (int) $this->printing_choice === PrintingChoice::BROWSER_POPUP;
    }

    public function isKotFormat(): bool
    {
        $format = (int) $this->print_format;

        return $format === PrintFormat::KOT || $format === PrintFormat::BOTH;
    }

    public function isInvoiceFormat(): bool
    {
        $format = (int) $this->print_format;

        return $format === PrintFormat::INVOICE || $format === PrintFormat::BOTH;
    }

    public function skipsAutoKot(): bool
    {
        return (int) $this->print_format === PrintFormat::NO_AUTO_KOT;
    }

    public function isBothFormat(): bool
    {
        return (int) $this->print_format === PrintFormat::BOTH;
    }

    public function opensCashDrawer(): bool
    {
        return (int) $this->open_cash_drawer === Ask::YES;
    }

    public function isNetwork(): bool
    {
        return (int) $this->printer_type === PrinterType::NETWORK;
    }

    public function isActive(): bool
    {
        return (int) $this->status === Status::ACTIVE;
    }
}
