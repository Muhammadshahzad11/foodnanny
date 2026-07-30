<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionBalanceResource extends JsonResource
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
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_collection_balance'      => AppLibrary::currencyAmountFormat($this->info['total_collection_balance']),
            'today_collection_balance'      => AppLibrary::currencyAmountFormat($this->info['today_collection_balance']),
            'last_week_collection_balance'  => AppLibrary::currencyAmountFormat($this->info['last_week_collection_balance']),
            'this_month_collection_balance' => AppLibrary::currencyAmountFormat($this->info['this_month_collection_balance'])
        ];
    }
}
