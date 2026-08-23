<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantDetailsResource extends JsonResource
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
            "id"                     => $this->id,
            "name"                   => $this->name,
            "email"                  => $this->email === null ? '' : $this->email,
            "phone"                  => $this->phone === null ? '' : $this->phone,
            "country_code"           => $this->country_code === null ? '' : $this->country_code,
            "user_id"                => $this->user_id,
            "user_name"              => $this->user?->name,
            "user_email"             => $this->user?->email,
            "user_phone"             => $this->user?->phone,
            "user_country_code"      => $this->user?->country_code,
            "user_image"             => $this->user?->image,
            "latitude"               => $this->latitude === null ? '' : $this->latitude,
            "longitude"              => $this->longitude === null ? '' : $this->longitude,
            "city"                   => $this->city,
            "state"                  => $this->state,
            "zip_code"               => $this->zip_code,
            "address"                => $this->address,
            "description"            => $this->description === null ? '' : $this->description,
            "status"                 => $this->status,
            "current_status"         => $this->current_status,
            "apply"                  => $this->apply,
            "image"                  => $this->image,
            "logo"                   => $this->logo,
            "online_commission"      => $this->online_commission === null ? '' : $this->online_commission,
            "flat_online_commission" => $this->online_commission === null ? '' : AppLibrary::flatAmountFormat($this->online_commission),
            "pos_commission"         => $this->pos_commission === null ? '' : $this->pos_commission,
            "flat_pos_commission"    => $this->pos_commission === null ? '' : AppLibrary::flatAmountFormat($this->pos_commission),
            "cuisine_id"             => RestaurantCuisineResource::collection($this->cuisines),
            "cuisine"                => AppLibrary::cuisineString($this->cuisines),
            "show_important_notice"  => $this->show_important_notice ?? \App\Enums\Ask::YES,
            "important_notice"       => $this->important_notice ?? '',
            "important_notice_emphasis" => $this->important_notice_emphasis ?? '',
            "show_highlights"        => $this->show_highlights ?? \App\Enums\Ask::YES,
            "highlights"             => $this->resource->normalizedHighlights(),
            "enable_pos"             => $this->enable_pos ?? \App\Enums\Ask::YES,
            "enable_kitchen"         => $this->enable_kitchen ?? \App\Enums\Ask::YES,
            "enable_waiter"          => $this->enable_waiter ?? \App\Enums\Ask::YES,
            "fssai_number"           => $this->fssai_number ?? '',
            "balance"                => AppLibrary::flatAmountFormat($this->balance),
            'convert_balance'        => AppLibrary::convertAmountFormat($this->balance),
            'currency_balance'       => AppLibrary::currencyAmountFormat($this->balance)
        ];
    }
}
