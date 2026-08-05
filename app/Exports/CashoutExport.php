<?php

namespace App\Exports;

use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Services\CashoutService;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CashoutExport implements FromCollection, WithHeadings
{
    public CashoutService $cashoutService;
    public PaginateRequest $request;

    public function __construct(CashoutService $cashoutService, $request)
    {
        $this->cashoutService = $cashoutService;
        $this->request        = $request;
    }

    public function collection() : \Illuminate\Support\Collection
    {
        $cashoutArray = [];
        $cashouts     = $this->cashoutService->list($this->request);

        foreach ($cashouts as $cashout) {
            $cashoutArray[] = [
                $cashout->user?->name . ' (' . $cashout->user?->email . ')',
                AppLibrary::convertAmountFormat($cashout->amount),
                AppLibrary::datetime($cashout->date)
            ];
        }
        return collect($cashoutArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.amount'),
            trans('all.label.date')
        ];
    }
}
