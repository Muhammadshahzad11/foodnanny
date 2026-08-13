<?php

namespace Tests\Unit;

use App\Enums\DeliveryChargeType;
use App\Enums\Status;
use App\Models\RestaurantDeliveryZone;
use App\Services\RestaurantDeliveryZoneService;
use PHPUnit\Framework\TestCase;

class RestaurantDeliveryZoneFeeTest extends TestCase
{
    public function test_fixed_charge_uses_min_plus_additional(): void
    {
        $service = new class extends RestaurantDeliveryZoneService {
            public function globalDeliverySetup(): array
            {
                return ['free_km' => 0, 'basic_fee' => 50, 'charge_per_kilo' => 10];
            }
        };

        $zone = new RestaurantDeliveryZone([
            'charge_type' => DeliveryChargeType::FIXED,
            'min_delivery_charge' => 100,
            'additional_delivery_charge' => 20,
            'status' => Status::ACTIVE,
        ]);

        $this->assertEquals(120.0, $service->calculateZoneFee($zone, 5));
    }

    public function test_per_km_uses_distance(): void
    {
        $service = new class extends RestaurantDeliveryZoneService {
            public function globalDeliverySetup(): array
            {
                return ['free_km' => 1, 'basic_fee' => 50, 'charge_per_kilo' => 10];
            }
        };

        $zone = new RestaurantDeliveryZone([
            'charge_type' => DeliveryChargeType::PER_KM,
            'min_delivery_charge' => 50,
            'charge_per_km' => 20,
            'additional_delivery_charge' => 0,
        ]);

        // distance 3km, free 1km → (2 * 20) + 50 = 90
        $this->assertEquals(90.0, $service->calculateZoneFee($zone, 3));
    }

    public function test_range_clamps_to_max(): void
    {
        $service = new class extends RestaurantDeliveryZoneService {
            public function globalDeliverySetup(): array
            {
                return ['free_km' => 0, 'basic_fee' => 50, 'charge_per_kilo' => 10];
            }
        };

        $zone = new RestaurantDeliveryZone([
            'charge_type' => DeliveryChargeType::RANGE,
            'min_delivery_charge' => 50,
            'max_delivery_charge' => 80,
            'charge_per_km' => 40,
            'additional_delivery_charge' => 0,
        ]);

        $this->assertEquals(80.0, $service->calculateZoneFee($zone, 5));
    }
}
