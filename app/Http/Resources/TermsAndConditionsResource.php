<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TermsAndConditionsResource extends JsonResource
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
            "terms_and_conditions_customer_page_id"     => $this->info['terms_and_conditions_customer_page_id'],
            "terms_and_conditions_restaurant_page_id"   => $this->info['terms_and_conditions_restaurant_page_id'],
            "terms_and_conditions_delivery_boy_page_id" => $this->info['terms_and_conditions_delivery_boy_page_id']
        ];
    }
}
