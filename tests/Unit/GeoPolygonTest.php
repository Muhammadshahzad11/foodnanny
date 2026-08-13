<?php

namespace Tests\Unit;

use App\Libraries\GeoPolygon;
use Tests\TestCase;

class GeoPolygonTest extends TestCase
{
    public function test_normalize_requires_three_points(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        GeoPolygon::normalize([
            ['lat' => 33.68, 'lng' => 73.04],
            ['lat' => 33.69, 'lng' => 73.05],
        ]);
    }

    public function test_normalize_closes_polygon(): void
    {
        $points = GeoPolygon::normalize([
            ['lat' => 33.68, 'lng' => 73.04],
            ['lat' => 33.69, 'lng' => 73.04],
            ['lat' => 33.69, 'lng' => 73.05],
        ]);

        $this->assertGreaterThanOrEqual(4, count($points));
        $this->assertEquals($points[0]['lat'], $points[count($points) - 1]['lat']);
        $this->assertEquals($points[0]['lng'], $points[count($points) - 1]['lng']);
    }

    public function test_point_inside_square(): void
    {
        $square = [
            ['lat' => 0, 'lng' => 0],
            ['lat' => 0, 'lng' => 10],
            ['lat' => 10, 'lng' => 10],
            ['lat' => 10, 'lng' => 0],
            ['lat' => 0, 'lng' => 0],
        ];

        $this->assertTrue(GeoPolygon::contains(5, 5, $square));
        $this->assertFalse(GeoPolygon::contains(15, 5, $square));
        $this->assertFalse(GeoPolygon::contains(5, 15, $square));
    }

    public function test_invalid_latitude_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        GeoPolygon::normalize([
            ['lat' => 120, 'lng' => 73],
            ['lat' => 33, 'lng' => 73],
            ['lat' => 34, 'lng' => 74],
        ]);
    }

    public function test_distance_km_reasonable(): void
    {
        $km = GeoPolygon::distanceKm(33.6844, 73.0479, 33.6944, 73.0479);
        $this->assertGreaterThan(0.9, $km);
        $this->assertLessThan(1.3, $km);
    }
}
