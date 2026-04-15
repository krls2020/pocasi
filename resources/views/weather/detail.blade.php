@extends('weather.layout')

@section('title', "Pocasi - {$log->city}")

@section('content')
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline text-sm">&larr; Zpet</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8 border mb-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $log->city }}</h1>
                @if($log->country)
                    <span class="text-lg text-gray-500">{{ $log->country }}</span>
                @endif
                @if($log->lat && $log->lon)
                    <p class="text-sm text-gray-400 mt-1">{{ $log->lat }}, {{ $log->lon }}</p>
                @endif
            </div>
            @if($log->icon)
                <img src="https://openweathermap.org/img/wn/{{ $log->icon }}@2x.png" alt="{{ $log->description }}" class="w-24 h-24">
            @endif
        </div>

        <div class="text-6xl font-bold text-gray-900 mb-2">
            {{ round($log->temperature) }}&deg;C
        </div>
        @if($log->description)
            <p class="text-xl text-gray-600 capitalize mb-6">{{ $log->description }}</p>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @if($log->feels_like !== null)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Pocitova teplota</div>
                    <div class="text-2xl font-bold text-gray-900">{{ round($log->feels_like) }}&deg;C</div>
                </div>
            @endif
            @if($log->humidity !== null)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Vlhkost</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $log->humidity }}%</div>
                </div>
            @endif
            @if($log->wind_speed !== null)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Vitr</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $log->wind_speed }} m/s</div>
                    @if($log->wind_deg !== null)
                        <div class="text-sm text-gray-400">{{ $log->wind_deg }}&deg;</div>
                    @endif
                </div>
            @endif
            @if($log->pressure !== null)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Tlak</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $log->pressure }} hPa</div>
                </div>
            @endif
            @if($log->visibility !== null)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Viditelnost</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($log->visibility / 1000, 1) }} km</div>
                </div>
            @endif
            @if($log->clouds !== null)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500">Oblacnost</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $log->clouds }}%</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Meta info --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Metadata zaznamu</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">ID zaznamu</dt>
                <dd class="font-medium text-gray-900">{{ $log->id }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Zdroj</dt>
                <dd class="font-medium text-gray-900">{{ $log->source }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Pozorovano</dt>
                <dd class="font-medium text-gray-900">{{ $log->observed_at?->format('d.m.Y H:i:s') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Zaznamenano</dt>
                <dd class="font-medium text-gray-900">{{ $log->created_at->format('d.m.Y H:i:s') }}</dd>
            </div>
        </dl>
    </div>

    {{-- Raw JSON --}}
    @if($log->raw_response)
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Raw API response</h2>
            <pre class="bg-gray-900 text-green-400 rounded-lg p-4 overflow-x-auto text-sm">{{ json_encode($log->raw_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
@endsection
