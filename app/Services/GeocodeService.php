<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class GeocodeService
{
    /**
     * @return array{lat: float, lon: float, display_name: string}|null
     */
    public static function geocode(?string $address): ?array
    {
        if (blank($address)) {
            return null;
        }

        $key = 'geocode:'.md5(trim($address));

        return Cache::remember($key, now()->addDays(30), function () use ($address): ?array {
            try {
                $response = Http::timeout(5)
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $address,
                        'format' => 'json',
                        'limit' => 1,
                    ]);

                if (! $response->successful()) {
                    return null;
                }

                $result = $response->json(0);

                if (blank($result) || ! isset($result['lat'], $result['lon'])) {
                    return null;
                }

                return [
                    'lat' => (float) $result['lat'],
                    'lon' => (float) $result['lon'],
                    'display_name' => (string) ($result['display_name'] ?? $address),
                ];
            } catch (Throwable) {
                return null;
            }
        });
    }
}
