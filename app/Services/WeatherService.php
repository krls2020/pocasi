<?php

namespace App\Services;

use App\Models\WeatherLog;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    private string $geocodeUrl = 'https://geocoding-api.open-meteo.com/v1/search';
    private string $forecastUrl = 'https://api.open-meteo.com/v1/forecast';

    public function fetchAndLog(string $city): ?WeatherLog
    {
        $location = $this->geocode($city);
        if (!$location) {
            return null;
        }

        $lat = $location['latitude'];
        $lon = $location['longitude'];
        $cityName = $location['name'] ?? $city;
        $countryCode = $location['country_code'] ?? null;

        $response = Http::timeout(10)->get($this->forecastUrl, [
            'latitude' => $lat,
            'longitude' => $lon,
            'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,wind_direction_10m,surface_pressure,cloud_cover',
            'wind_speed_unit' => 'ms',
            'timezone' => 'auto',
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        $current = $data['current'] ?? [];
        $weatherCode = (int) ($current['weather_code'] ?? 0);

        return WeatherLog::create([
            'city' => $cityName,
            'country' => $countryCode,
            'temperature' => $current['temperature_2m'] ?? 0,
            'feels_like' => $current['apparent_temperature'] ?? null,
            'humidity' => $current['relative_humidity_2m'] ?? null,
            'wind_speed' => $current['wind_speed_10m'] ?? null,
            'wind_deg' => $current['wind_direction_10m'] ?? null,
            'pressure' => isset($current['surface_pressure']) ? (int) round($current['surface_pressure']) : null,
            'visibility' => null,
            'clouds' => $current['cloud_cover'] ?? null,
            'description' => self::weatherDescription($weatherCode),
            'icon' => (string) $weatherCode,
            'lat' => $lat,
            'lon' => $lon,
            'source' => 'open-meteo',
            'raw_response' => $data,
            'observed_at' => isset($current['time']) ? \Carbon\Carbon::parse($current['time']) : now(),
        ]);
    }

    public function fetchForecast(float $lat, float $lon): ?array
    {
        $response = Http::timeout(10)->get($this->forecastUrl, [
            'latitude' => $lat,
            'longitude' => $lon,
            'daily' => 'temperature_2m_max,temperature_2m_min,weather_code,precipitation_sum,wind_speed_10m_max',
            'wind_speed_unit' => 'ms',
            'timezone' => 'auto',
            'forecast_days' => 7,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        $daily = $data['daily'] ?? [];

        if (empty($daily['time'])) {
            return null;
        }

        $forecast = [];
        foreach ($daily['time'] as $i => $date) {
            $code = (int) ($daily['weather_code'][$i] ?? 0);
            $forecast[] = [
                'date' => $date,
                'temp_max' => $daily['temperature_2m_max'][$i] ?? null,
                'temp_min' => $daily['temperature_2m_min'][$i] ?? null,
                'weather_code' => $code,
                'precipitation' => $daily['precipitation_sum'][$i] ?? 0,
                'wind_max' => $daily['wind_speed_10m_max'][$i] ?? null,
                'description' => self::weatherDescription($code),
                'emoji' => self::weatherEmoji($code),
            ];
        }

        return $forecast;
    }

    public function isConfigured(): bool
    {
        return true;
    }

    private function geocode(string $city): ?array
    {
        $response = Http::timeout(10)->get($this->geocodeUrl, [
            'name' => $city,
            'count' => 1,
            'language' => 'cs',
        ]);

        if (!$response->successful() || empty($response->json('results'))) {
            return null;
        }

        return $response->json('results.0');
    }

    public static function weatherDescription(int $code): string
    {
        return match (true) {
            $code === 0 => 'Jasno',
            $code === 1 => 'Převážně jasno',
            $code === 2 => 'Polojasno',
            $code === 3 => 'Zataženo',
            in_array($code, [45, 48]) => 'Mlha',
            in_array($code, [51, 53, 55]) => 'Mrholení',
            in_array($code, [56, 57]) => 'Mrznoucí mrholení',
            in_array($code, [61, 63, 65]) => 'Déšť',
            in_array($code, [66, 67]) => 'Mrznoucí déšť',
            in_array($code, [71, 73, 75]) => 'Sněžení',
            $code === 77 => 'Sněhové krupky',
            in_array($code, [80, 81, 82]) => 'Přeháňky',
            in_array($code, [85, 86]) => 'Sněhové přeháňky',
            $code === 95 => 'Bouřka',
            in_array($code, [96, 99]) => 'Bouřka s kroupami',
            default => 'Neznámo',
        };
    }

    public static function weatherEmoji(int $code): string
    {
        return match (true) {
            $code === 0 => '☀️',
            $code === 1 => '🌤️',
            $code === 2 => '⛅',
            $code === 3 => '☁️',
            in_array($code, [45, 48]) => '🌫️',
            in_array($code, [51, 53, 55, 56, 57]) => '🌦️',
            in_array($code, [61, 63, 65, 66, 67, 80, 81, 82]) => '🌧️',
            in_array($code, [71, 73, 75, 77, 85, 86]) => '🌨️',
            in_array($code, [95, 96, 99]) => '⛈️',
            default => '🌤️',
        };
    }
}
