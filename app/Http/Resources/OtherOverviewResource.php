<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class OtherOverviewResource extends JsonResource
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
            "delivered_orders"  => $this->info['delivered_orders'],
            "canceled_orders"   => $this->info['canceled_orders'],
            "returned_orders"   => $this->info['returned_orders'],
            "rejected_orders"   => $this->info['rejected_orders'],
        ];
    }
}
