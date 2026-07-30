<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOrderSetupResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "delivery"                     => $this->delivery,
            "takeaway"                     => $this->takeaway,
            "schedule_order_slot_duration" => $this->schedule_order_slot_duration,
            "food_preparation_time"        => $this->food_preparation_time,
            "minimum_order_limit"          => $this->minimum_order_limit,
            "currency_minimum_order_limit" => AppLibrary::currencyAmountFormat($this->minimum_order_limit),

        ];

    }
}
