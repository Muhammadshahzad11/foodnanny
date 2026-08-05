<?php

namespace App\Exports;


use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Services\CampaignAndOfferService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RestaurantCampaignExport implements FromCollection, WithHeadings
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
        $campaignArray  = [];
        $campaignsArray = $this->campaignAndOfferService->campaignList($this->request);

        foreach ($campaignsArray as $campaign) {
            $campaignArray[] = [
                $campaign->title,
                AppLibrary::date($campaign->start_date) . ' - ' . AppLibrary::date($campaign->end_date),
                AppLibrary::time($campaign->start_time) . ' - ' . AppLibrary::time($campaign->end_time),
                trans('campaign_type.' . $campaign->type),
                $campaign->amount,
                trans('statuse.' . $campaign->status),
            ];
        }
        return collect($campaignArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.title'),
            trans('all.label.date'),
            trans('all.label.time'),
            trans('all.label.type'),
            trans('all.label.amount'),
            trans('all.label.status'),
        ];
    }
}