<?php

namespace App\Http\Resources;


use App\Enums\Ask;
use App\Libraries\AppLibrary;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantOfferResource extends JsonResource
{
    use DefaultAccessModelTrait;
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
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
            'restaurant_id' => $this->is_single === Ask::NO ? null : $this->offerRestaurants[0]?->restaurant_id,
            'image'         => $this->cover,
            'apply'         => $this->restaurants()->where('restaurant_id', $this->restaurant())->exists() ? Ask::YES : Ask::NO,
            'apply_status'  => $this->offerRestaurants()->where('restaurant_id', $this->restaurant())->value('status'),
        ];
    }
}