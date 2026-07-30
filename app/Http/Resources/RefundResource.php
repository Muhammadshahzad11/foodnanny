<?php

namespace App\Http\Resources;

use App\Enums\ModelType;
use App\Models\Restaurant;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundResource extends JsonResource
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
            'id'               => $this->id,
            'order_serial_no'  => $this->order_serial_no,
            'refund_amount'    => AppLibrary::flatAmountFormat($this->refund_amount),
            'deduction_amount' => AppLibrary::flatAmountFormat($this->deduction_amount),
            'responsible'      => $this->responsible_type == Restaurant::class ? ModelType::RESTAURANT : ModelType::DELIVERY_BOY,
            'datetime'         => AppLibrary::datetime($this->created_at),
            'info'             => json_decode($this->info)
        ];
    }
}
