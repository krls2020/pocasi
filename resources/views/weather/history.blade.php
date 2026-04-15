@extends('weather.layout')

@section('title', 'Historie počasí')

@section('content')
    <div class="glass rounded-2xl overflow-hidden fade-in">
        <div class="px-6 py-5 border-b border-white/[0.06] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h1 class="text-xl font-bold">Historie počasí</h1>
            @if($cities->isNotEmpty())
                <form action="{{ route('weather.history') }}" method="GET">
                    <select name="city"
                            class="bg-white/8 border border-white/15 text-white rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 focus:outline-none cursor-pointer min-w-[180px]"
                            style="background-color: rgba(255,255,255,0.08);"
                            onchange="this.form.submit()">
                        <option value="" style="background:#1e1b4b;">Všechna města</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ $selectedCity === $city ? 'selected' : '' }} style="background:#1e1b4b;">{{ $city }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        @if($logs->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/[0.06]">
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider">Město</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider">Teplota</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden sm:table-cell">Pocit</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden md:table-cell">Počasí</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden md:table-cell">Vlhkost</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden lg:table-cell">Vítr</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-white/40 uppercase tracking-wider hidden lg:table-cell">Tlak</th>
                            <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-white/40 uppercase tracking-wider">Čas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.04]">
                        @foreach($logs as $log)
                            <tr class="hover:bg-white/[0.03] transition">
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <a href="{{ route('weather.detail', $log) }}" class="text-blue-300/90 hover:text-blue-200 font-medium flex items-center gap-2.5">
                                        <span class="text-base">{{ $log->weather_emoji }}</span>
                                        <span>
                                            {{ $log->city }}
                                            @if($log->country)
                                                <span class="text-white/20 text-xs ml-1">{{ $log->country }}</span>
                                            @endif
                                        </span>
                                    </a>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap font-bold text-white/90">{{ round($log->temperature) }}°C</td>
                                <td class="px-5 py-3 whitespace-nowrap text-white/40 hidden sm:table-cell">{{ $log->feels_like !== null ? round($log->feels_like) . '°C' : '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-white/40 capitalize hidden md:table-cell">{{ $log->description ?? '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-white/40 hidden md:table-cell">{{ $log->humidity !== null ? $log->humidity . '%' : '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-white/40 hidden lg:table-cell">{{ $log->wind_speed !== null ? $log->wind_speed . ' m/s' : '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-white/40 hidden lg:table-cell">{{ $log->pressure !== null ? $log->pressure . ' hPa' : '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-white/25 text-sm text-right">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-white/[0.06] flex items-center justify-between">
                    <span class="text-xs text-white/30">
                        {{ $logs->firstItem() }}–{{ $logs->lastItem() }} z {{ $logs->total() }}
                    </span>
                    <div class="flex gap-2">
                        @if($logs->onFirstPage())
                            <span class="px-4 py-2 rounded-lg text-xs bg-white/[0.03] text-white/20 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        @else
                            <a href="{{ $logs->withQueryString()->previousPageUrl() }}" class="px-4 py-2 rounded-lg text-xs bg-white/[0.06] hover:bg-white/[0.12] transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif
                        @if($logs->hasMorePages())
                            <a href="{{ $logs->withQueryString()->nextPageUrl() }}" class="px-4 py-2 rounded-lg text-xs bg-white/[0.06] hover:bg-white/[0.12] transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="px-4 py-2 rounded-lg text-xs bg-white/[0.03] text-white/20 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="p-16 text-center">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-white/40">Žádné záznamy{{ $selectedCity ? " pro město {$selectedCity}" : '' }}.</p>
            </div>
        @endif
    </div>
@endsection
