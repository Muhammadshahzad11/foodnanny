<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantOwnerOverviewResource extends JsonResource
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
            "total_sales"                   => AppLibrary::currencyAmountFormat($this->info['total_sales']),
            "total_orders"                  => $this->info['total_orders'],
            "available_balance"             => AppLibrary::currencyAmountFormat($this->info['available_balance']?->balance),
            "total_menu_items"              => $this->info['total_menu_items'],
            "filter_total_orders"           => $this->info['filter_total_orders'],
            "filter_total_pending"          => $this->info['filter_total_pending'],
            "filter_total_preparing"        => $this->info['filter_total_preparing'],
            "filter_total_out_for_delivery" => $this->info['filter_total_out_for_delivery'],
            "filter_total_delivered"        => $this->info['filter_total_delivered'],
            "filter_total_canceled"         => $this->info['filter_total_canceled'],
            "filter_total_returned"         => $this->info['filter_total_returned'],
            "filter_total_rejected"         => $this->info['filter_total_rejected'],
        ];
    }
}
