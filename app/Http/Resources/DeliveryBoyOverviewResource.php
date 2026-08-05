<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryBoyOverviewResource extends JsonResource
{
    public array $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    public function toArray($request): array
    {
        return [
            "total_earnings"        => AppLibrary::currencyAmountFormat($this->info['total_earnings']),
            "total_accepted_orders" => $this->info['total_accepted_orders'],
            "completed_delivery"    => $this->info['completed_delivery'],
            "return_delivery"       => $this->info['return_delivery'],
        ];
    }
}
