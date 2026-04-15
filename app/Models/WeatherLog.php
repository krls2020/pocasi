<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherLog extends Model
{
    protected $fillable = [
        'city',
        'country',
        'temperature',
        'feels_like',
        'humidity',
        'wind_speed',
        'wind_deg',
        'pressure',
        'visibility',
        'clouds',
        'description',
        'icon',
        'lat',
        'lon',
        'source',
        'raw_response',
        'observed_at',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:2',
            'feels_like' => 'decimal:2',
            'wind_speed' => 'decimal:2',
            'lat' => 'decimal:7',
            'lon' => 'decimal:7',
            'raw_response' => 'array',
            'observed_at' => 'datetime',
        ];
    }
}
