<?php

namespace App\Libraries;

class GeoPolygon
{
    /**
     * Normalize and validate a polygon of lat/lng points.
     *
     * @param  array<int, mixed>  $points
     * @return array<int, array{lat: float, lng: float}>
     */
    public static function normalize(array $points): array
    {
        $normalized = [];

        foreach ($points as $point) {
            if (!is_array($point)) {
                continue;
            }

            $lat = $point['lat'] ?? $point['latitude'] ?? null;
            $lng = $point['lng'] ?? $point['longitude'] ?? $point['lon'] ?? null;

            if ($lat === null || $lng === null) {
                continue;
            }

            $lat = (float) $lat;
            $lng = (float) $lng;

            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                throw new \InvalidArgumentException(trans('all.message.invalid_zone_coordinates'));
            }

            $normalized[] = [
                'lat' => $lat,
                'lng' => $lng,
            ];
        }

        if (count($normalized) < 3) {
            throw new \InvalidArgumentException(trans('all.message.zone_polygon_min_points'));
        }

        // Close polygon if first != last
        $first = $normalized[0];
        $last  = $normalized[count($normalized) - 1];
        if (abs($first['lat'] - $last['lat']) > 0.0000001 || abs($first['lng'] - $last['lng']) > 0.0000001) {
            $normalized[] = $first;
        }

        return $normalized;
    }

    /**
     * Ray-casting point-in-polygon test.
     *
     * @param  array<int, array{lat: float, lng: float}>  $polygon
     */
    public static function contains(float $lat, float $lng, array $polygon): bool
    {
        $n = count($polygon);
        if ($n < 3) {
            return false;
        }

        // Ensure closed for algorithm
        $first = $polygon[0];
        $last  = $polygon[$n - 1];
        if (abs($first['lat'] - $last['lat']) > 0.0000001 || abs($first['lng'] - $last['lng']) > 0.0000001) {
            $polygon[] = $first;
            $n++;
        }

        $inside = false;
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $yi = (float) $polygon[$i]['lng'];
            $xi = (float) $polygon[$i]['lat'];
            $yj = (float) $polygon[$j]['lng'];
            $xj = (float) $polygon[$j]['lat'];

            $intersect = (($yi > $lng) !== ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / (($yj - $yi) ?: 1e-12) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * Haversine distance in kilometers.
     */
    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat        = deg2rad($lat2 - $lat1);
        $dLng        = deg2rad($lng2 - $lng1);
        $a           = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }
}
