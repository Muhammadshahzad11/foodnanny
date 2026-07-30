<?php

namespace App\Exports;


use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Services\CampaignAndOfferService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RestaurantOfferExport implements FromCollection, WithHeadings
{

    public CampaignAndOfferService $campaignAndOfferService;
    public PaginateRequest $request;

    public function __construct(CampaignAndOfferService $campaignAndOfferService, $request)
    {
        $this->campaignAndOfferService = $campaignAndOfferService;
        $this->request      = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $offerArray  = [];
        $offersArray = $this->campaignAndOfferService->offerList($this->request);

        foreach ($offersArray as $offer) {
            $offerArray[] = [
                $offer->title,
                AppLibrary::flatAmountFormat($offer->amount) . ' %',
                AppLibrary::date($offer->start_date) . ' - ' . AppLibrary::date($offer->end_date),
                AppLibrary::time($offer->start_time) . ' - ' . AppLibrary::time($offer->end_time),
                trans('statuse.' . $offer->status),
            ];
        }
        return collect($offerArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.title'),
            trans('all.label.discount'),
            trans('all.label.date'),
            trans('all.label.time'),
            trans('all.label.status'),
        ];
    }
}