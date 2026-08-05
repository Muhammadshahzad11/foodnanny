<?php

namespace App\Exports;

use App\Services\RestaurantOwnerService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class RestaurantOwnerExport implements FromCollection, WithHeadings
{

    public RestaurantOwnerService $restaurantOwnerService;
    public PaginateRequest $request;

    public function __construct(RestaurantOwnerService $restaurantOwnerService, $request)
    {
        $this->restaurantOwnerService = $restaurantOwnerService;
        $this->request         = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $restaurantOwnerArray = [];
        $restaurantOwners     = $this->restaurantOwnerService->list($this->request);

        foreach ($restaurantOwners as $restaurantOwner) {
            $restaurantOwnerArray[] = [
                $restaurantOwner->name,
                $restaurantOwner->email,
                $restaurantOwner->phone ? $restaurantOwner->country_code . '' . $restaurantOwner->phone : null,
                trans('statuse.' . $restaurantOwner->status)
            ];
        }
        return collect($restaurantOwnerArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.email'),
            trans('all.label.phone'),
            trans('all.label.status')
        ];
    }
}
