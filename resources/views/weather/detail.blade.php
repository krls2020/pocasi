@extends('weather.layout')

@section('title', "Počasí — {$log->city}")

@section('content')
    <div class="mb-6 fade-in">
        <a href="{{ route('weather.dashboard') }}" class="text-white/40 hover:text-white/70 text-sm flex items-center gap-1.5 w-fit transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Dashboard
        </a>
    </div>

    {{-- Main weather card --}}
    <div class="glass rounded-2xl p-6 sm:p-10 mb-6 fade-in fade-d1">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-1">{{ $log->city }}</h1>
                <div class="flex items-center gap-3 text-white/40">
                    @if($log->country)
                        <span class="text-sm font-medium uppercase tracking-wider">{{ $log->country }}</span>
                    @endif
                    @if($log->lat && $log->lon)
                        <span class="text-xs">{{ round($log->lat, 2) }}° {{ round($log->lon, 2) }}°</span>
                    @endif
                </div>
            </div>
            <span class="weather-emoji-lg">{{ $log->weather_emoji }}</span>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3 mb-10">
            <div class="temp-display text-7xl sm:text-8xl font-black leading-none">
                {{ round($log->temperature) }}°
            </div>
            @if($log->description)
                <p class="text-xl sm:text-2xl text-white/50 capitalize sm:pb-3">{{ $log->description }}</p>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @if($log->feels_like !== null)
                <div class="glass-light rounded-xl p-4">
                    <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1">Pocitová</div>
                    <div class="text-xl font-bold">{{ round($log->feels_like) }}°C</div>
                </div>
            @endif
            @if($log->humidity !== null)
                <div class="glass-light rounded-xl p-4">
                    <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1">Vlhkost</div>
                    <div class="text-xl font-bold">{{ $log->humidity }}<span class="text-sm text-white/50">%</span></div>
                </div>
            @endif
            @if($log->wind_speed !== null)
                <div class="glass-light rounded-xl p-4">
                    <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1">Vítr</div>
                    <div class="text-xl font-bold">{{ $log->wind_speed }} <span class="text-sm text-white/50">m/s</span></div>
                    @if($log->wind_deg !== null)
                        <div class="text-[10px] text-white/30 mt-0.5">{{ $log->wind_deg }}°</div>
                    @endif
                </div>
            @endif
            @if($log->pressure !== null)
                <div class="glass-light rounded-xl p-4">
                    <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1">Tlak</div>
                    <div class="text-xl font-bold">{{ $log->pressure }} <span class="text-sm text-white/50">hPa</span></div>
                </div>
            @endif
            @if($log->visibility !== null)
                <div class="glass-light rounded-xl p-4">
                    <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1">Viditelnost</div>
                    <div class="text-xl font-bold">{{ number_format($log->visibility / 1000, 1) }} <span class="text-sm text-white/50">km</span></div>
                </div>
            @endif
            @if($log->clouds !== null)
                <div class="glass-light rounded-xl p-4">
                    <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1">Oblačnost</div>
                    <div class="text-xl font-bold">{{ $log->clouds }}<span class="text-sm text-white/50">%</span></div>
                </div>
            @endif
        </div>
    </div>

    {{-- Forecast --}}
    @if($forecast)
        <div class="glass rounded-2xl p-5 sm:p-6 mb-6 fade-in fade-d2">
            <h2 class="text-sm font-semibold text-white/50 uppercase tracking-wider mb-5">Předpověď 7 dní</h2>
            <div class="grid grid-cols-7 gap-2 sm:gap-3">
                @foreach($forecast as $i => $day)
                    <div class="text-center {{ $i === 0 ? 'glass-light rounded-xl' : '' }} p-2 sm:p-3">
                        <div class="text-[10px] sm:text-xs text-white/40 mb-1 font-semibold uppercase">
                            {{ \Carbon\Carbon::parse($day['date'])->locale('cs')->isoFormat('dd') }}
                        </div>
                        <div class="text-xs text-white/20 mb-1.5">
                            {{ \Carbon\Carbon::parse($day['date'])->format('d.m.') }}
                        </div>
                        <div class="text-xl sm:text-2xl mb-1.5">{{ $day['emoji'] }}</div>
                        <div class="font-bold text-sm">{{ round($day['temp_max']) }}°</div>
                        <div class="text-white/30 text-xs">{{ round($day['temp_min']) }}°</div>
                        @if($day['precipitation'] > 0)
                            <div class="text-blue-300/60 text-[10px] mt-1">{{ round($day['precipitation'], 1) }}mm</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- History for this city --}}
    @if($history->count() > 1)
        <div class="glass rounded-2xl overflow-hidden mb-6 fade-in fade-d3">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h2 class="text-sm font-semibold text-white/50 uppercase tracking-wider">Historie — {{ $log->city }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/[0.06]">
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider">Počasí</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider">Teplota</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden sm:table-cell">Vlhkost</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden sm:table-cell">Vítr</th>
                            <th class="px-5 py-3 text-right text-[11px] font-semibold text-white/40 uppercase tracking-wider">Zaznamenáno</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.04]">
                        @foreach($history as $entry)
                            <tr class="{{ $entry->id === $log->id ? 'bg-white/[0.04]' : '' }} hover:bg-white/[0.03] transition">
                                <td class="px-5 py-2.5 whitespace-nowrap">
                                    <span class="flex items-center gap-2">
                                        <span class="text-base">{{ $entry->weather_emoji }}</span>
                                        <span class="capitalize text-white/50 text-sm">{{ $entry->description ?? '—' }}</span>
                                    </span>
                                </td>
                                <td class="px-5 py-2.5 whitespace-nowrap font-bold text-white/90">{{ round($entry->temperature) }}°C</td>
                                <td class="px-5 py-2.5 whitespace-nowrap text-white/40 text-sm hidden sm:table-cell">{{ $entry->humidity !== null ? $entry->humidity . '%' : '—' }}</td>
                                <td class="px-5 py-2.5 whitespace-nowrap text-white/40 text-sm hidden sm:table-cell">{{ $entry->wind_speed !== null ? $entry->wind_speed . ' m/s' : '—' }}</td>
                                <td class="px-5 py-2.5 whitespace-nowrap text-white/25 text-sm text-right">{{ $entry->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Metadata --}}
    <div class="glass rounded-2xl p-6 fade-in fade-d4">
        <h2 class="text-sm font-semibold text-white/50 uppercase tracking-wider mb-4">Metadata</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="glass-light rounded-xl p-3">
                <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">ID</div>
                <div class="text-sm font-medium mt-1">{{ $log->id }}</div>
            </div>
            <div class="glass-light rounded-xl p-3">
                <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">Zdroj</div>
                <div class="text-sm font-medium mt-1">{{ $log->source }}</div>
            </div>
            <div class="glass-light rounded-xl p-3">
                <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">Pozorováno</div>
                <div class="text-sm font-medium mt-1">{{ $log->observed_at?->format('d.m.Y H:i') ?? '—' }}</div>
            </div>
            <div class="glass-light rounded-xl p-3">
                <div class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">Zaznamenáno</div>
                <div class="text-sm font-medium mt-1">{{ $log->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>
    </div>

    @if($log->raw_response)
        <details class="mt-4 fade-in">
            <summary class="text-white/25 text-xs cursor-pointer hover:text-white/40 transition py-2">
                Raw API odpověď
            </summary>
            <div class="glass rounded-xl p-4 mt-1">
                <pre class="text-green-300/60 text-[11px] overflow-x-auto leading-relaxed font-mono">{{ json_encode($log->raw_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </details>
    @endif
@endsection
