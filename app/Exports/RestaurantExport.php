<?php

namespace App\Exports;

use App\Http\Requests\PaginateRequest;
use App\Services\RestaurantService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RestaurantExport implements FromCollection, WithHeadings
{

    public RestaurantService $restaurantService;
    public PaginateRequest $request;

    public function __construct(RestaurantService $restaurantService, $request)
    {
        $this->restaurantService = $restaurantService;
        $this->request      = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $restaurantArray  = [];
        $restaurantsArray = $this->restaurantService->list($this->request);

        foreach ($restaurantsArray as $restaurant) {
            $restaurantArray[] = [
                $restaurant->name,
                $restaurant->email,
                $restaurant->phone,
                $restaurant->latitude,
                $restaurant->longitude,
                $restaurant->city,
                $restaurant->state,
                $restaurant->zip_code,
                $restaurant->address,
                trans('statuse.' . $restaurant->status)
            ];
        }
        return collect($restaurantArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.email'),
            trans('all.label.phone'),
            trans('all.label.latitude'),
            trans('all.label.longitude'),
            trans('all.label.city'),
            trans('all.label.state'),
            trans('all.label.zip_code'),
            trans('all.label.address'),
            trans('all.label.status')
        ];
    }
}
