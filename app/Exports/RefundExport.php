<?php

namespace App\Exports;

use App\Libraries\AppLibrary;
use App\Models\Restaurant;
use App\Services\RefundService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class RefundExport implements FromCollection, WithHeadings
{

    public RefundService $refundService;
    public PaginateRequest $request;

    public function __construct(RefundService $refundService, $request)
    {
        $this->refundService = $refundService;
        $this->request       = $request;
    }

    public function collection(): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        $i           = 0;
        $refundArray = [];
        $refunds     = $this->refundService->list($this->request);
        foreach ($refunds as $refund) {
            $refundArray[$i] = [
                $refund->order_serial_no,
                $refund->refund_amount,
                $refund->deduction_amount,
                AppLibrary::datetime($refund->created_at),
                $refund->responsible_type == Restaurant::class ? trans('all.label.restaurant') : trans('all.label.delivery_boy')
            ];
            $i++;
        }
        return collect($refundArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.order_id'),
            trans('all.label.refund_amount'),
            trans('all.label.deduction_amount'),
            trans('all.label.date'),
            trans('all.label.responsible')
        ];
    }
}
