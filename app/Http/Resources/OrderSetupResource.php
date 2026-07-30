<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSetupResource extends JsonResource
{
    public $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        if (isset($this->info[0]['food_preparation_time'])) {
            return [
                "restaurant_id"                => $this->info[0]['restaurant_id'],
                "food_preparation_time"        => $this->info[0]['food_preparation_time'],
                "schedule_order_slot_duration" => $this->info[0]['schedule_order_slot_duration'],
                "takeaway"                     => $this->info[0]['takeaway'],
                "delivery"                     => $this->info[0]['delivery'],
                "minimum_order_limit"          => AppLibrary::flatAmountFormat($this->info[0]['minimum_order_limit']),
            ];
        } else {
            return [
                "restaurant_id"                => '',
                "food_preparation_time"        => '',
                "schedule_order_slot_duration" => '',
                "takeaway"                     => '',
                "delivery"                     => '',
                "minimum_order_limit"          => ''
            ];
        }
    }
}
