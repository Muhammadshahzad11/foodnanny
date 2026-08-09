<?php

namespace App\Http\Requests;

use App\Enums\Ask;
use App\Enums\PrintFormat;
use App\Enums\PrinterType;
use App\Enums\PrintingChoice;
use App\Enums\Status;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrinterRequest extends FormRequest
{
    use DefaultAccessModelTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $choice = (int) $this->input('printing_choice', PrintingChoice::BROWSER_POPUP);
        $type   = (int) $this->input('printer_type', PrinterType::NETWORK);
        $direct = $choice === PrintingChoice::DIRECT_PRINT;
        $directNetwork = $direct && $type === PrinterType::NETWORK;
        $directWindows = $direct && $type === PrinterType::WINDOWS_SHARED;

        return [
            'name'                 => ['required', 'string', 'max:190'],
            'printing_choice'      => ['required', 'numeric', Rule::in([PrintingChoice::BROWSER_POPUP, PrintingChoice::DIRECT_PRINT])],
            'print_format'         => ['required', 'numeric', Rule::in([PrintFormat::INVOICE, PrintFormat::KOT, PrintFormat::NO_AUTO_KOT, PrintFormat::BOTH])],
            'printer_type'         => ['required', 'numeric', Rule::in([PrinterType::WINDOWS_SHARED, PrinterType::NETWORK])],
            'characters_per_line'  => ['required', 'integer', 'min:24', 'max:80'],
            'open_cash_drawer'     => ['required', 'numeric', Rule::in([Ask::YES, Ask::NO])],
            'invoice_qr_status'    => ['required', 'numeric', Rule::in([Ask::YES, Ask::NO])],
            'computer_ipv4'        => [$direct ? 'nullable' : 'nullable', 'ip'],
            'printer_ip'           => [$directNetwork ? 'required' : 'nullable', 'ip'],
            'printer_port'         => [$directNetwork ? 'required' : 'nullable', 'integer', 'min:1', 'max:65535'],
            'windows_printer_name' => [$directWindows ? 'required' : 'nullable', 'string', 'max:190'],
            'status'               => ['required', 'numeric', Rule::in([Status::ACTIVE, Status::INACTIVE])],
        ];
    }

    public function attributes(): array
    {
        return [
            'printing_choice'      => 'printing choice',
            'print_format'         => 'print format',
            'printer_type'         => 'printer type',
            'computer_ipv4'        => 'computer ipv4',
            'printer_ip'           => 'printer ip',
            'printer_port'         => 'printer port',
            'windows_printer_name' => 'windows printer name',
            'open_cash_drawer'     => 'open cash drawer',
            'invoice_qr_status'    => 'invoice qr status',
        ];
    }
}
