<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminSimpleOfferResource extends JsonResource
{
    /**
     * Admin list resource: translated (localized) values via the model accessor,
     * no raw values and no translations array (that lives on the edit/detail resource).
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'tag'           => $this->tag,
            "slug"          => $this->slug,
            'description'   => $this->description,
            'location'      => $this->location === null ? '' : $this->location,
            'latitude'      => $this->latitude === null ? '' : $this->latitude,
            'longitude'     => $this->longitude === null ? '' : $this->longitude,
            'amount'        => $this->amount === null ? 0 : $this->amount,
            "flat_amount"   => AppLibrary::flatAmountFormat($this->amount),
            'status'        => $this->status,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'convert_date'  => AppLibrary::date($this->start_date) . ' - ' . AppLibrary::date($this->end_date),
            'start_time'    => $this->start_time,
            'end_time'      => $this->end_time,
            'convert_time'  => AppLibrary::time($this->start_time) . ' - ' . AppLibrary::time($this->end_time),
            'type'          => $this->type,
            'single'        => $this->is_single,
            'restaurant_id' => $this->is_single === Ask::NO ? null : (isset($this->offerRestaurants[0]) ? $this->offerRestaurants[0]->restaurant_id : null),
            'thumbnail'     => $this->thumb,
            'cover'         => $this->cover
        ];
    }
}
