@extends('weather.layout')

@section('title', 'Weather Dashboard')

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="text-sm font-medium text-gray-500">Celkem zaznamu</div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['total_logs']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="text-sm font-medium text-gray-500">Sledovana mesta</div>
            <div class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['cities_tracked'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="text-sm font-medium text-gray-500">Posledni aktualizace</div>
            <div class="mt-1 text-xl font-bold text-gray-900">
                {{ $stats['last_update'] ? \Carbon\Carbon::parse($stats['last_update'])->diffForHumans() : 'Zadna data' }}
            </div>
        </div>
    </div>

    {{-- Fetch form --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Nacist pocasi</h2>
        @if(!$apiConfigured)
            <div class="rounded-lg bg-yellow-50 p-4 text-yellow-800 border border-yellow-200 mb-4">
                API klic neni nastaven. Nastavte promennou <code class="bg-yellow-100 px-1 rounded">OPENWEATHERMAP_API_KEY</code> v konfiguraci sluzby.
            </div>
        @endif
        <form action="{{ route('weather.fetch') }}" method="POST" class="flex gap-4">
            @csrf
            <input type="text" name="city" placeholder="Zadejte mesto (napr. Praha, Brno, London)"
                   class="flex-1 rounded-lg border-gray-300 border px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   required value="{{ old('city') }}">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium {{ !$apiConfigured ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ !$apiConfigured ? 'disabled' : '' }}>
                Nacist
            </button>
        </form>
        @error('city')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Current weather cards --}}
    @if($latest->isNotEmpty())
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aktualni pocasi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($latest as $log)
                <a href="{{ route('weather.detail', $log) }}" class="bg-white rounded-xl shadow-sm p-6 border hover:shadow-md transition block">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $log->city }}</h3>
                            @if($log->country)
                                <span class="text-sm text-gray-500">{{ $log->country }}</span>
                            @endif
                        </div>
                        @if($log->icon)
                            <img src="https://openweathermap.org/img/wn/{{ $log->icon }}@2x.png" alt="{{ $log->description }}" class="w-16 h-16">
                        @endif
                    </div>

                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        {{ round($log->temperature) }}&deg;C
                    </div>

                    @if($log->description)
                        <p class="text-gray-600 capitalize mb-3">{{ $log->description }}</p>
                    @endif

                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-500">
                        @if($log->feels_like !== null)
                            <div>Pocitova: <span class="font-medium text-gray-700">{{ round($log->feels_like) }}&deg;C</span></div>
                        @endif
                        @if($log->humidity !== null)
                            <div>Vlhkost: <span class="font-medium text-gray-700">{{ $log->humidity }}%</span></div>
                        @endif
                        @if($log->wind_speed !== null)
                            <div>Vitr: <span class="font-medium text-gray-700">{{ $log->wind_speed }} m/s</span></div>
                        @endif
                        @if($log->pressure !== null)
                            <div>Tlak: <span class="font-medium text-gray-700">{{ $log->pressure }} hPa</span></div>
                        @endif
                    </div>

                    <div class="mt-3 text-xs text-gray-400">
                        {{ $log->created_at->diffForHumans() }}
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    {{-- Recent logs table --}}
    @if($recentLogs->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Posledni zaznamy</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teplota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Popis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vlhkost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vitr</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('weather.detail', $log) }}" class="text-blue-600 hover:underline font-medium">
                                        {{ $log->city }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ round($log->temperature) }}&deg;C</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 capitalize">{{ $log->description ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $log->humidity !== null ? $log->humidity . '%' : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $log->wind_speed !== null ? $log->wind_speed . ' m/s' : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-400 text-sm">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 border text-center">
            <div class="text-gray-400 text-6xl mb-4">&#9925;</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Zatim zadna data</h3>
            <p class="text-gray-500">Zadejte mesto vyse a nacetete prvni zaznam pocasi.</p>
        </div>
    @endif
@endsection
