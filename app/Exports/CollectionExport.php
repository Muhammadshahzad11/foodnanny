<?php

namespace App\Exports;

use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Services\CollectionService;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CollectionExport implements FromCollection, WithHeadings
{
    public CollectionService $collectionService;
    public PaginateRequest $request;

    public function __construct(CollectionService $collectionService, $request)
    {
        $this->collectionService = $collectionService;
        $this->request           = $request;
    }

    public function collection(): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        $collectionArray = [];
        $collections     = $this->collectionService->list($this->request);

        foreach ($collections as $collection) {
            $collectionArray[] = [
                $collection->user?->name . ' (' . $collection->user?->email . ')',
                AppLibrary::datetime($collection->date),
                AppLibrary::convertAmountFormat($collection->amount)
            ];
        }
        return collect($collectionArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.date'),
            trans('all.label.amount')
        ];
    }
}
