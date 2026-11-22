<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiService
{
    /**
     * Fetch current weather data from Open-Meteo API.
     * Free, no API key required, reliable worldwide coverage.
     *
     * @return array{success: bool, source: string, temperature: ?float, wind_speed: ?float, weather_condition: string, humidity: ?int, is_fallback: bool}
     */
    public function getWeather(float $latitude, float $longitude): array
    {
        $cacheKey = sprintf('weather_%.2f_%.2f', $latitude, $longitude);

        return Cache::remember($cacheKey, 1800, function () use ($latitude, $longitude) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'CargoTrace-Platform/1.0'])
                    ->get('https://api.open-meteo.com/v1/forecast', [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];

                    $code = $current['weather_code'] ?? 0;
                    $condition = $this->interpretWeatherCode($code);

                    return [
                        'success' => true,
                        'source' => 'Open-Meteo Weather API',
                        'temperature' => $current['temperature_2m'] ?? null,
                        'wind_speed' => $current['wind_speed_10m'] ?? null,
                        'humidity' => $current['relative_humidity_2m'] ?? null,
                        'weather_code' => $code,
                        'weather_condition' => $condition,
                        'is_fallback' => false,
                    ];
                }

                Log::warning('Weather API returned non-200 status: ' . $response->status());
            } catch (\Throwable $e) {
                Log::warning('Weather API request failed: ' . $e->getMessage());
            }

            // Graceful fallback data as required by Slide 9
            return [
                'success' => false,
                'source' => 'Cached/Simulated Ambient Data (Offline Fallback)',
                'temperature' => 24.5,
                'wind_speed' => 12.0,
                'humidity' => 65,
                'weather_code' => 1,
                'weather_condition' => 'Mainly Clear (Estimated)',
                'is_fallback' => true,
            ];
        });
    }

    /**
     * Map WMO Weather interpretation codes (WW) to human descriptions.
     */
    private function interpretWeatherCode(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear Sky',
            in_array($code, [1, 2, 3]) => 'Partly Cloudy',
            in_array($code, [45, 48]) => 'Foggy / Low Visibility',
            in_array($code, [51, 53, 55]) => 'Light Drizzle',
            in_array($code, [61, 63, 65]) => 'Rain Showers',
            in_array($code, [71, 73, 75]) => 'Snow Fall',
            in_array($code, [80, 81, 82]) => 'Heavy Rain',
            in_array($code, [95, 96, 99]) => 'Thunderstorm & Rough Seas',
            default => 'Fair Weather',
        };
    }

    /**
     * Forward geocoding query to OpenStreetMap Nominatim.
     */
    public function geocode(string $query): array
    {
        $cacheKey = 'geocode_' . md5(strtolower(trim($query)));

        return Cache::remember($cacheKey, 86400, function () use ($query) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'CargoTrace-Platform/1.0 (contact: info@cargotrace.test)'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $query,
                        'format' => 'json',
                        'limit' => 1,
                    ]);

                if ($response->successful() && !empty($response->json())) {
                    $item = $response->json()[0];
                    return [
                        'success' => true,
                        'name' => $item['display_name'] ?? $query,
                        'latitude' => (float) $item['lat'],
                        'longitude' => (float) $item['lon'],
                        'is_fallback' => false,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Geocoding request failed: ' . $e->getMessage());
            }

            return [
                'success' => false,
                'name' => $query,
                'latitude' => 0.0,
                'longitude' => 0.0,
                'is_fallback' => true,
            ];
        });
    }
}
