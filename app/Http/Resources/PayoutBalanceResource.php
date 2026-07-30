<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutBalanceResource extends JsonResource
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
            'total_payout_balance'      => AppLibrary::currencyAmountFormat($this->info['total_payout_balance']),
            'today_payout_balance'      => AppLibrary::currencyAmountFormat($this->info['today_payout_balance']),
            'this_week_payout_balance'  => AppLibrary::currencyAmountFormat($this->info['this_week_payout_balance']),
            'this_month_payout_balance' => AppLibrary::currencyAmountFormat($this->info['this_month_payout_balance'])
        ];
    }
}
