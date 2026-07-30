<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminOverviewResource extends JsonResource
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
            "total_orders"      => $this->info['total_orders'],
            "sales_volume"      => AppLibrary::currencyAmountFormat($this->info['sales_volume']),
            "commission"        => AppLibrary::currencyAmountFormat($this->info['commission']),
            "no_of_restaurants" => $this->info['no_of_restaurants'],
            "delivered_orders"  => $this->info['delivered_orders'],
            "canceled_orders"   => $this->info['canceled_orders'],
            "returned_orders"   => $this->info['returned_orders'],
            "rejected_orders"   => $this->info['rejected_orders']
        ];
    }
}
