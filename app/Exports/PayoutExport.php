<?php

namespace App\Exports;

use App\Libraries\AppLibrary;
use App\Services\PayoutService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayoutExport implements FromCollection, WithHeadings
{

    public PayoutService $payoutService;
    public PaginateRequest $request;

    public function __construct(PayoutService $payoutService, $request)
    {
        $this->payoutService = $payoutService;
        $this->request       = $request;
    }

    public function collection(): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        $payoutArray  = [];
        $payoutsArray = $this->payoutService->list($this->request);

        foreach ($payoutsArray as $payout) {
            $payoutArray[] = [
                $payout->model?->name,
                $payout->model?->email,
                $payout->model?->phone,
                AppLibrary::datetime($payout->date),
                $payout->sign . " " . AppLibrary::flatAmountFormat($payout->amount)
            ];
        }
        return collect($payoutArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.email'),
            trans('all.label.phone'),
            trans('all.label.date'),
            trans('all.label.amount')
        ];
    }
}
