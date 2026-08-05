<?php

namespace App\Exports;

use App\Libraries\AppLibrary;
use App\Services\StatementService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class StatementExport implements FromCollection, WithHeadings
{

    public StatementService $statementService;
    public PaginateRequest $request;

    public function __construct(StatementService $statementService, $request)
    {
        $this->statementService = $statementService;
        $this->request          = $request;
    }

    public function collection(): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        $statementArray  = [];
        $statementsArray = $this->statementService->list($this->request);

        foreach ($statementsArray as $statement) {
            $statementArray[] = [
                AppLibrary::datetime($statement->date),
                $statement->order?->order_serial_no,
                trans('statement_type.' . $statement->type),
                trans('statement_detail.' . $statement->detail),
                $statement->sign . " " . AppLibrary::flatAmountFormat(abs($statement->amount)),
            ];
        }
        return collect($statementArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.date'),
            trans('all.label.order_serial_no'),
            trans('all.label.type'),
            trans('all.label.detail'),
            trans('all.label.amount')
        ];
    }
}
