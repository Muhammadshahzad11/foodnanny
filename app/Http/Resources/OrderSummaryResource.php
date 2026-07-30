<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class OrderSummaryResource extends JsonResource
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
            "delivered" => $this->info['delivered'],
            "returned"  => $this->info['returned'],
            "canceled"  => $this->info['canceled'],
            "rejected"  => $this->info['rejected'],
        ];
    }
}
