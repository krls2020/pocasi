<?php

namespace App\Services;

use App\Models\WeatherLog;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openweathermap.org/data/2.5/weather';

    public function __construct()
    {
        $this->apiKey = (string) config('services.openweathermap.key', '');
    }

    public function fetchAndLog(string $city): ?WeatherLog
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $response = Http::timeout(10)->get($this->baseUrl, [
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'lang' => 'cz',
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        return WeatherLog::create([
            'city' => $data['name'] ?? $city,
            'country' => $data['sys']['country'] ?? null,
            'temperature' => $data['main']['temp'] ?? 0,
            'feels_like' => $data['main']['feels_like'] ?? null,
            'humidity' => $data['main']['humidity'] ?? null,
            'wind_speed' => $data['wind']['speed'] ?? null,
            'wind_deg' => $data['wind']['deg'] ?? null,
            'pressure' => $data['main']['pressure'] ?? null,
            'visibility' => $data['visibility'] ?? null,
            'clouds' => $data['clouds']['all'] ?? null,
            'description' => $data['weather'][0]['description'] ?? null,
            'icon' => $data['weather'][0]['icon'] ?? null,
            'lat' => $data['coord']['lat'] ?? null,
            'lon' => $data['coord']['lon'] ?? null,
            'source' => 'openweathermap',
            'raw_response' => $data,
            'observed_at' => isset($data['dt']) ? \Carbon\Carbon::createFromTimestamp($data['dt']) : now(),
        ]);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
