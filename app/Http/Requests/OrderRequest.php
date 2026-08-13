<?php

namespace App\Http\Requests;

use App\Enums\Activity;
use App\Enums\OrderType;
use App\Models\FrontendAddress;
use App\Models\Restaurant;
use App\Rules\ValidJsonOrder;
use App\Services\DineInOrderGuard;
use App\Libraries\GeoPolygon;
use App\Services\RestaurantDeliveryZoneService;
use App\Services\ZoneService;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orderType = (int) $this->input('order_type');
        $isDelivery = $orderType === OrderType::DELIVERY;
        $isDining   = $orderType === OrderType::DINING_TABLE;

        return [
            'restaurant_id'      => ['required', 'numeric'],
            'subtotal'           => ['required', 'numeric'],
            'discount'           => ['nullable', 'numeric'],
            'delivery_fee'       => $isDelivery ? ['required', 'numeric'] : ['nullable'],
            'extra_delivery_fee' => ['nullable', 'numeric'],
            'total'              => ['required', 'numeric'],
            'tax'                => ['required', 'numeric'],
            'order_type'         => [
                'required',
                'numeric',
                Rule::in([OrderType::DELIVERY, OrderType::TAKEAWAY, OrderType::DINING_TABLE]),
            ],
            'is_advance_order'   => ['required', 'numeric'],
            'address_id'         => $isDelivery ? ['required', 'numeric'] : ['nullable'],
            'delivery_time'      => $isDelivery ? ['required', 'string'] : ['nullable'],
            'coupon_id'          => ['nullable', 'numeric'],
            'payment_method'     => ['required', 'numeric'],
            'source'             => ['required', 'numeric'],
            'cutlery'            => ['required', 'numeric'],
            'service_fee'        => ['required', 'numeric'],
            'rider_tip'          => ['required', 'numeric'],
            'table_id'           => $isDining ? ['required', 'integer', 'exists:restaurant_tables,id'] : ['nullable', 'integer'],
            'qr_token'           => $isDining ? ['required', 'string', 'size:64'] : ['nullable', 'string'],
            'items'              => ['required', 'json', new ValidJsonOrder],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ((int) $this->input('restaurant_id') <= 0) {
                $validator->errors()->add('restaurant_id', trans('all.message.restaurant_not_found'));

                return;
            }

            $restaurant = Restaurant::with('orderSetup')->where(['id' => $this->restaurant_id])->first();
            if (blank($restaurant)) {
                $validator->errors()->add('restaurant_id', trans('all.message.restaurant_not_found'));

                return;
            }

            $orderType = (int) $this->input('order_type');

            if ($orderType === OrderType::DELIVERY && $restaurant->orderSetup?->delivery == Activity::DISABLE) {
                $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
            } elseif ($orderType === OrderType::TAKEAWAY && $restaurant->orderSetup?->takeaway == Activity::DISABLE) {
                $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
            } elseif ($orderType === OrderType::DINING_TABLE) {
                try {
                    app(DineInOrderGuard::class)->assertOrderableTable(
                        (int) $this->input('restaurant_id'),
                        (int) $this->input('table_id'),
                        $this->input('qr_token')
                    );
                } catch (\Exception $exception) {
                    $validator->errors()->add('table_id', $exception->getMessage());
                }
            } elseif (blank($this->input('order_type'))) {
                $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
            }

            if ($orderType === OrderType::DELIVERY) {
                $this->validateDeliveryZone($validator, (int) $restaurant->id);
            }
        });
    }

    protected function validateDeliveryZone($validator, int $restaurantId): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $addressId = (int) $this->input('address_id');
        if ($addressId <= 0) {
            return;
        }

        $address = FrontendAddress::query()->find($addressId);
        if (!$address || blank($address->latitude) || blank($address->longitude)) {
            return;
        }

        $lat = (float) $address->latitude;
        $lng = (float) $address->longitude;

        $platformZoneService = app(ZoneService::class);
        $platformZone = $platformZoneService->detect($lat, $lng);

        if ($platformZoneService->hasActiveZones()) {
            $restaurant = Restaurant::withoutGlobalScopes()->find($restaurantId);
            if (!$platformZone || !$restaurant || (int) $restaurant->zone_id !== (int) $platformZone->id) {
                $validator->errors()->add('address_id', trans('all.message.restaurant_does_not_deliver'));
                return;
            }

            $distance = 0.0;
            if (!blank($restaurant->latitude) && !blank($restaurant->longitude)) {
                $distance = GeoPolygon::distanceKm(
                    $lat,
                    $lng,
                    (float) $restaurant->latitude,
                    (float) $restaurant->longitude
                );
            }

            $resolved = $platformZoneService->calculateFee($platformZone, $distance, (float) $this->input('subtotal'));
            if (!$resolved['available']) {
                $validator->errors()->add('address_id', trans('all.message.restaurant_does_not_deliver'));
                return;
            }

            $this->applyAuthoritativeFee($resolved['fee'], null);
            $this->merge(['zone_id' => $platformZone->id]);
            return;
        }

        /** @var RestaurantDeliveryZoneService $zoneService */
        $zoneService = app(RestaurantDeliveryZoneService::class);

        $radius = null;
        try {
            $radius = (float) (Settings::group('site')->get('site_delivery_boy_order_radius') ?: 0);
            if ($radius <= 0) {
                $radius = null;
            }
        } catch (\Throwable $e) {
            $radius = null;
        }

        if (!$zoneService->isDeliverable($restaurantId, $lat, $lng, $radius)) {
            $validator->errors()->add('address_id', trans('all.message.restaurant_does_not_deliver'));
            return;
        }

        // Authoritative fee when restaurant has active zones. Preserve free-delivery coupon pattern.
        if ($zoneService->restaurantHasActiveZones($restaurantId)) {
            $resolved = $zoneService->resolveDelivery($restaurantId, $lat, $lng);
            if (!$resolved['available']) {
                $validator->errors()->add('address_id', trans('all.message.restaurant_does_not_deliver'));
                return;
            }

            $this->applyAuthoritativeFee((float) $resolved['fee'], $resolved['zone']?->id);
        }
    }

    protected function applyAuthoritativeFee(float $fee, ?int $deliveryZoneId): void
    {
        $extra = (float) ($this->input('extra_delivery_fee') ?? 0);
        $clientFee = (float) ($this->input('delivery_fee') ?? 0);

        if ($extra > 0 && $clientFee == 0.0) {
            $this->merge([
                'delivery_fee'       => 0,
                'extra_delivery_fee' => $fee,
                'delivery_zone_id'   => $deliveryZoneId,
            ]);
            return;
        }

        $diff = $fee - $clientFee;
        $this->merge([
            'delivery_fee'     => $fee,
            'delivery_zone_id' => $deliveryZoneId,
            'total'            => round((float) $this->input('total') + $diff, 6),
        ]);
    }

    public function attributes(): array
    {
        return [
            'address_id' => strtolower(trans('all.label.address')),
            'table_id'   => strtolower(trans('all.label.table')),
        ];
    }
}
