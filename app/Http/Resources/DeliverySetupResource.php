<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliverySetupResource extends JsonResource
{
    public array $info;

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
        return [
            "delivery_setup_free_delivery_kilometer" => $this->info['delivery_setup_free_delivery_kilometer'],
            "delivery_setup_basic_delivery_fee"      => $this->info['delivery_setup_basic_delivery_fee'],
            "delivery_setup_charge_per_kilo"         => $this->info['delivery_setup_charge_per_kilo']
        ];
    }
}
