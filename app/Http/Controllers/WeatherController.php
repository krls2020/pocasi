<?php

namespace App\Http\Controllers;

use App\Models\WeatherLog;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function dashboard(WeatherService $weather)
    {
        $latest = WeatherLog::query()
            ->selectRaw('DISTINCT ON (city) *')
            ->orderBy('city')
            ->orderByDesc('created_at')
            ->get();

        $recentLogs = WeatherLog::query()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $stats = [
            'total_logs' => WeatherLog::count(),
            'cities_tracked' => WeatherLog::distinct('city')->count('city'),
            'last_update' => WeatherLog::max('created_at'),
        ];

        return view('weather.dashboard', [
            'latest' => $latest,
            'recentLogs' => $recentLogs,
            'stats' => $stats,
            'apiConfigured' => $weather->isConfigured(),
        ]);
    }

    public function fetch(Request $request, WeatherService $weather)
    {
        $request->validate([
            'city' => 'required|string|max:100',
        ]);

        $log = $weather->fetchAndLog($request->input('city'));

        if (!$log) {
            return back()->with('error', 'Nepodařilo se získat data o počasí. Zkontrolujte API klíč a název města.');
        }

        return back()->with('success', "Počasí pro {$log->city} úspěšně načteno.");
    }

    public function history(Request $request)
    {
        $query = WeatherLog::query()->orderByDesc('created_at');

        if ($city = $request->input('city')) {
            $query->where('city', 'ilike', "%{$city}%");
        }

        $logs = $query->paginate(25);
        $cities = WeatherLog::distinct()->pluck('city')->sort();

        return view('weather.history', [
            'logs' => $logs,
            'cities' => $cities,
            'selectedCity' => $city,
        ]);
    }

    public function detail(WeatherLog $weatherLog)
    {
        return view('weather.detail', ['log' => $weatherLog]);
    }

    public function status()
    {
        $result = ['service' => 'appdev', 'status' => 'ok', 'connections' => []];

        try {
            $t = microtime(true);
            \DB::select('SELECT 1');
            $ms = round((microtime(true) - $t) * 1000);
            $result['connections']['db'] = ['status' => 'ok', 'latency_ms' => $ms];
        } catch (\Exception $e) {
            $result['connections']['db'] = ['status' => 'error', 'error' => $e->getMessage()];
        }

        return response()->json($result);
    }
}
