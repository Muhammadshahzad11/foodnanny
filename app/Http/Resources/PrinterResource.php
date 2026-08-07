<?php

namespace App\Http\Resources;

use App\Enums\PrintFormat;
use App\Enums\PrinterType;
use App\Enums\PrintingChoice;
use Illuminate\Http\Resources\Json\JsonResource;

class PrinterResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'restaurant_id'        => $this->restaurant_id,
            'name'                 => $this->name,
            'outlet'               => $this->restaurant?->name,
            'printing_choice'      => $this->printing_choice,
            'printing_choice_label'=> PrintingChoice::LABELS[$this->printing_choice] ?? null,
            'print_format'         => $this->print_format,
            'print_format_label'   => PrintFormat::LABELS[$this->print_format] ?? null,
            'printer_type'         => $this->printer_type,
            'printer_type_label'   => PrinterType::LABELS[$this->printer_type] ?? null,
            'characters_per_line'  => $this->characters_per_line,
            'open_cash_drawer'     => $this->open_cash_drawer,
            'invoice_qr_status'    => $this->invoice_qr_status,
            'computer_ipv4'        => $this->computer_ipv4,
            'printer_ip'           => $this->printer_ip,
            'printer_port'         => $this->printer_port,
            'ip_display'           => $this->printer_ip
                ? ($this->printer_ip . ':' . ($this->printer_port ?: 9100))
                : null,
            'connection_status'    => $this->connection_status ?? null,
            'connection_label'     => $this->connection_label ?? null,
            'is_connected'         => $this->is_connected ?? null,
            'status'               => $this->status,
            'kitchens_count'       => $this->whenCounted('kitchens'),
        ];
    }
}
