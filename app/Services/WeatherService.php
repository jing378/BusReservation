<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * OpenWeatherMap lookups with a 30-minute cache and a 2-hour fallback cache for outages.
 */
class WeatherService
{
    private const FRESH_SECONDS = 1800;

    private const STALE_SECONDS = 7200;

    /**
     * @return array<string, mixed>
     */
    public function forCity(string $city): array
    {
        $key = 'weather:'.md5(mb_strtolower($city));

        return $this->getCachedWeather($key, ['q' => $city]);
    }

    /**
     * @return array<string, mixed>
     */
    public function forCoordinates(float $latitude, float $longitude): array
    {
        $key = 'weather:'.md5($latitude.','.$longitude);

        return $this->getCachedWeather($key, [
            'lat' => $latitude,
            'lon' => $longitude,
        ]);
    }

    /**
     * @param  array<string, float|string>  $query
     * @return array<string, mixed>
     */
    private function getCachedWeather(string $key, array $query): array
    {
        if ($fresh = Cache::get($key.':fresh')) {
            return $fresh + ['cached' => true];
        }

        $result = $this->fetch($query);

        if ($result['success']) {
            Cache::put($key.':fresh', $result, self::FRESH_SECONDS);
            Cache::put($key.':stale', $result, self::STALE_SECONDS);

            return $result + ['cached' => false];
        }

        if ($stale = Cache::get($key.':stale')) {
            return $stale + ['cached' => true, 'api_error' => true];
        }

        return $result + ['cached' => false];
    }

    /**
     * @param  array<string, float|string>  $query
     * @return array<string, mixed>
     */
    private function fetch(array $query): array
    {
        $apiKey = config('services.openweather.key');

        if (! $apiKey) {
            return ['success' => false, 'error' => 'Weather API key is not configured.'];
        }

        try {
            $response = Http::connectTimeout(3)
                ->timeout(6)
                ->retry(2, 1000, throw: false)
                ->get('https://api.openweathermap.org/data/2.5/weather', [
                    ...$query,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]);
        } catch (ConnectionException) {
            return ['success' => false, 'error' => 'No internet connection available. No cached data found.', 'offline' => true];
        } catch (Throwable $exception) {
            return ['success' => false, 'error' => 'Weather API unavailable: '.$exception->getMessage()];
        }

        $weather = $response->json('weather.0');
        $main = $response->json('main');

        if (! $response->ok() || ! $weather || ! $main) {
            return ['success' => false, 'error' => 'Weather API unavailable: HTTP '.$response->status()];
        }

        return [
            'success' => true,
            'data' => [
                'temperature' => round($main['temp']),
                'condition' => $weather['main'],
                'description' => $weather['description'],
                'icon' => $weather['icon'],
                'location' => $response->json('name'),
                'humidity' => $main['humidity'],
                'wind_speed' => round(($response->json('wind.speed') ?? 0) * 3.6),
                'feels_like' => round($main['feels_like'] ?? $main['temp']),
            ],
        ];
    }
}
