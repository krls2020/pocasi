@extends('weather.layout')

@section('title', 'Počasí — Dashboard')

@section('content')
    {{-- Search --}}
    <div class="mb-12 fade-in">
        <div class="text-center mb-8">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-3">
                <span class="temp-display">Sledujte počasí</span>
            </h1>
            <p class="text-white/40 text-lg">Zadejte město a získejte aktuální data i předpověď</p>
        </div>
        <form action="{{ route('weather.fetch') }}" method="POST" class="max-w-2xl mx-auto">
            @csrf
            <div class="glass rounded-2xl p-2 flex gap-2 glow-blue">
                <div class="relative flex-1">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="city" placeholder="Praha, Brno, London, Tokyo..."
                           class="w-full bg-transparent text-white placeholder-white/30 pl-12 pr-4 py-3.5 rounded-xl focus:outline-none focus:bg-white/5 transition text-lg"
                           required value="{{ old('city') }}" autocomplete="off">
                </div>
                <button type="submit"
                        class="bg-indigo-500 hover:bg-indigo-400 text-white px-8 py-3.5 rounded-xl font-semibold transition-all hover:shadow-lg hover:shadow-indigo-500/25 whitespace-nowrap active:scale-95">
                    Načíst
                </button>
            </div>
            @error('city')
                <p class="mt-3 text-sm text-red-400 text-center">{{ $message }}</p>
            @enderror
        </form>
    </div>

    {{-- Stats --}}
    @if($stats['total_logs'] > 0)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10 fade-in fade-d1">
            <div class="glass rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center text-xl">📊</div>
                <div>
                    <div class="text-white/40 text-xs uppercase tracking-wider font-medium">Záznamy</div>
                    <div class="text-2xl font-bold mt-0.5">{{ number_format($stats['total_logs']) }}</div>
                </div>
            </div>
            <div class="glass rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center text-xl">🏙️</div>
                <div>
                    <div class="text-white/40 text-xs uppercase tracking-wider font-medium">Města</div>
                    <div class="text-2xl font-bold mt-0.5">{{ $stats['cities_tracked'] }}</div>
                </div>
            </div>
            <div class="glass rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center text-xl">🕐</div>
                <div>
                    <div class="text-white/40 text-xs uppercase tracking-wider font-medium">Aktualizace</div>
                    <div class="text-lg font-bold mt-0.5">
                        {{ $stats['last_update'] ? \Carbon\Carbon::parse($stats['last_update'])->diffForHumans() : '—' }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Weather cards --}}
    @if($latest->isNotEmpty())
        <div class="mb-10 fade-in fade-d2">
            <h2 class="text-sm font-semibold text-white/50 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Aktuální počasí
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($latest as $log)
                    <a href="{{ route('weather.detail', $log) }}" class="glass rounded-2xl p-6 card-hover block group">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold group-hover:text-blue-200 transition">{{ $log->city }}</h3>
                                @if($log->country)
                                    <span class="text-xs text-white/40 font-medium uppercase tracking-wider">{{ $log->country }}</span>
                                @endif
                            </div>
                            <span class="weather-emoji">{{ $log->weather_emoji }}</span>
                        </div>

                        <div class="temp-display text-5xl font-black mb-1 leading-none">
                            {{ round($log->temperature) }}°
                        </div>
                        @if($log->description)
                            <p class="text-white/50 capitalize mb-5 text-sm">{{ $log->description }}</p>
                        @endif

                        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            @if($log->feels_like !== null)
                                <div class="flex items-center gap-2 text-white/40">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Pocit {{ round($log->feels_like) }}°
                                </div>
                            @endif
                            @if($log->humidity !== null)
                                <div class="flex items-center gap-2 text-white/40">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a8 8 0 01-8-8c0-4 8-12 8-12s8 8 8 12a8 8 0 01-8 8z"/></svg>
                                    {{ $log->humidity }}%
                                </div>
                            @endif
                            @if($log->wind_speed !== null)
                                <div class="flex items-center gap-2 text-white/40">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5a2 2 0 012 2H6m10 4a2 2 0 002-2H4m12 8a2 2 0 01-2 2H8"/></svg>
                                    {{ $log->wind_speed }} m/s
                                </div>
                            @endif
                            @if($log->pressure !== null)
                                <div class="flex items-center gap-2 text-white/40">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0h6m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v10m6 0v-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4"/></svg>
                                    {{ $log->pressure }} hPa
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 pt-4 border-t border-white/5 text-xs text-white/25 flex items-center justify-between">
                            <span>{{ $log->created_at->diffForHumans() }}</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 7-day forecast --}}
    @if($forecast)
        <div class="mb-10 fade-in fade-d3">
            <h2 class="text-sm font-semibold text-white/50 uppercase tracking-wider mb-4">
                Předpověď 7 dní — {{ $forecastCity }}
            </h2>
            <div class="glass rounded-2xl p-4 sm:p-6">
                <div class="grid grid-cols-7 gap-2 sm:gap-4">
                    @foreach($forecast as $i => $day)
                        <div class="text-center {{ $i === 0 ? 'glass-light rounded-xl p-2 sm:p-3' : 'p-2 sm:p-3' }}">
                            <div class="text-[10px] sm:text-xs text-white/40 mb-1.5 font-semibold uppercase">
                                {{ \Carbon\Carbon::parse($day['date'])->locale('cs')->isoFormat('dd') }}
                            </div>
                            <div class="text-xl sm:text-3xl mb-1.5">{{ $day['emoji'] }}</div>
                            <div class="font-bold text-sm sm:text-base">{{ round($day['temp_max']) }}°</div>
                            <div class="text-white/30 text-xs">{{ round($day['temp_min']) }}°</div>
                            @if($day['precipitation'] > 0)
                                <div class="text-blue-300/70 text-[10px] mt-1">{{ round($day['precipitation'], 1) }}mm</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Recent logs --}}
    @if($recentLogs->isNotEmpty())
        <div class="fade-in fade-d4">
            <h2 class="text-sm font-semibold text-white/50 uppercase tracking-wider mb-4">Poslední záznamy</h2>
            <div class="glass rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/8">
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider">Město</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider">Teplota</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden sm:table-cell">Počasí</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden md:table-cell">Vlhkost</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden md:table-cell">Vítr</th>
                                <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-white/40 uppercase tracking-wider">Čas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04]">
                            @foreach($recentLogs as $log)
                                <tr class="hover:bg-white/[0.03] transition">
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <a href="{{ route('weather.detail', $log) }}" class="text-blue-300/90 hover:text-blue-200 font-medium flex items-center gap-2.5">
                                            <span class="text-base">{{ $log->weather_emoji }}</span>
                                            {{ $log->city }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap font-bold text-white/90">{{ round($log->temperature) }}°C</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-white/40 capitalize hidden sm:table-cell">{{ $log->description ?? '—' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-white/40 hidden md:table-cell">{{ $log->humidity !== null ? $log->humidity . '%' : '—' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-white/40 hidden md:table-cell">{{ $log->wind_speed !== null ? $log->wind_speed . ' m/s' : '—' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap text-white/25 text-sm text-right">{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif($latest->isEmpty())
        <div class="glass rounded-2xl p-16 text-center fade-in fade-d1">
            <div class="text-7xl mb-5">🌍</div>
            <h3 class="text-2xl font-bold mb-2">Začněte sledovat počasí</h3>
            <p class="text-white/40 max-w-md mx-auto">Zadejte název města do vyhledávání výše a načtěte první záznam. Data jsou z Open-Meteo — zcela zdarma.</p>
        </div>
    @endif
@endsection
