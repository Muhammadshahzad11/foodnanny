<?php

namespace App\Exports;

use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Services\CampaignService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CampaignExport implements FromCollection, WithHeadings
{
    public CampaignService $campaignService;
    public PaginateRequest $request;

    public function __construct(CampaignService $campaignService, $request)
    {
        $this->campaignService = $campaignService;
        $this->request      = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $campaignArray  = [];
        $campaignsArray = $this->campaignService->list($this->request);

        foreach ($campaignsArray as $campaign) {
            $campaignArray[] = [
                $campaign->title,
                AppLibrary::date($campaign->start_date),
                AppLibrary::date($campaign->end_date),
                AppLibrary::time($campaign->start_time),
                AppLibrary::time($campaign->end_time),
                trans('campaign_type.' . $campaign->type),
                $campaign->amount,
                trans('statuse.' . $campaign->status)
            ];
        }
        return collect($campaignArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.title'),
            trans('all.label.start_date'),
            trans('all.label.end_date'),
            trans('all.label.start_time'),
            trans('all.label.end_time'),
            trans('all.label.type'),
            trans('all.label.amount'),
            trans('all.label.status')
        ];
    }
}
