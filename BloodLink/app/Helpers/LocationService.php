<?php

namespace App\Helpers;

class LocationService
{
    public static function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public static function filterNearby(object $items, float $originLat, float $originLon, float $maxDistance = 25): array
    {
        return collect($items)
            ->filter(function ($item) use ($originLat, $originLon, $maxDistance) {
                if ($item->latitude === null || $item->longitude === null) {
                    return false;
                }
                $item->distance = round(self::haversineDistance($originLat, $originLon, $item->latitude, $item->longitude), 1);

                return $item->distance <= $maxDistance;
            })
            ->sortBy('distance')
            ->values()
            ->all();
    }
}
