<?php

namespace App\Exports;


use App\Libraries\AppLibrary;
use App\Services\AvailableOrderService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AvailableOrderExport implements FromCollection, WithHeadings
{

    public AvailableOrderService $availableOrderService;
    public PaginateRequest $request;

    public function __construct(AvailableOrderService $availableOrderService, $request)
    {
        $this->availableOrderService = $availableOrderService;
        $this->request               = $request;
    }

    public function collection()
    {
        $orderArray  = [];
        $ordersArray = $this->availableOrderService->list($this->request);

        foreach ($ordersArray as $order) {
            $orderArray[] = [
                $order->order_serial_no,
                AppLibrary::flatAmountFormat($order->total),
                AppLibrary::datetime($order->order_datetime),
                trans('order_status.' . $order->status)
            ];
        }
        return collect($orderArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.order_serial_no'),
            trans('all.label.amount'),
            trans('all.label.date'),
            trans('all.label.status'),
        ];
    }
}
