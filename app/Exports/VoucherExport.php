<?php

namespace App\Exports;

use App\Enums\DiscountType;
use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Services\VoucherService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VoucherExport implements FromCollection, WithHeadings
{
    public VoucherService $voucherService;
    public PaginateRequest $request;

    public function __construct(VoucherService $voucherService, $request)
    {
        $this->voucherService = $voucherService;
        $this->request        = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $voucherArray = [];
        $vouchersArray     = $this->voucherService->list($this->request);

        foreach ($vouchersArray as $voucher) {
            $voucherArray[] = [
                $voucher->name,
                $voucher->code,
                $voucher->discount,
                $voucher->discount_type == DiscountType::FIXED ? 'Fixed' : 'Percentage',
                AppLibrary::datetime($voucher->start_date),
                AppLibrary::datetime($voucher->end_date)
            ];
        }
        return collect($voucherArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.code'),
            trans('all.label.discount'),
            trans('all.label.discount_type'),
            trans('all.label.start_date'),
            trans('all.label.end_date')
        ];
    }
}
