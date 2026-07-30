<?php

namespace App\Exports;


use App\Libraries\AppLibrary;
use App\Http\Requests\PaginateRequest;
use App\Services\ReturnOrderService;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReturnOrderExport implements FromCollection, WithHeadings
{

    public ReturnOrderService $returnOrderService;
    public PaginateRequest $request;

    public function __construct(ReturnOrderService $returnOrderService, $request)
    {
        $this->returnOrderService = $returnOrderService;
        $this->request            = $request;
    }

    public function collection(): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        $orderArray  = [];
        $ordersArray = $this->returnOrderService->list($this->request);

        foreach ($ordersArray as $order) {
            $orderArray[] = [
                $order->order_serial_no,
                optional($order->user)->name,
                AppLibrary::flatAmountFormat($order->total),
                AppLibrary::datetime($order->order_datetime)
            ];
        }
        return collect($orderArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.order_id'),
            trans('all.label.customer'),
            trans('all.label.amount'),
            trans('all.label.date')
        ];
    }
}
