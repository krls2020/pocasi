@extends('weather.layout')

@section('title', 'Historie pocasi')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Historie pocasi</h2>
            <form action="{{ route('weather.history') }}" method="GET" class="flex gap-3">
                <select name="city" class="rounded-lg border-gray-300 border px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Vsechna mesta</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ $selectedCity === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($logs->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teplota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pocitova</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Popis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vlhkost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vitr</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tlak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pozorovano</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zaznamenano</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $log->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('weather.detail', $log) }}" class="text-blue-600 hover:underline font-medium">
                                        {{ $log->city }} {{ $log->country ? "({$log->country})" : '' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ round($log->temperature) }}&deg;C</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $log->feels_like !== null ? round($log->feels_like) . '°C' : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 capitalize">{{ $log->description ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $log->humidity !== null ? $log->humidity . '%' : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $log->wind_speed !== null ? $log->wind_speed . ' m/s' : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $log->pressure !== null ? $log->pressure . ' hPa' : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->observed_at?->format('d.m.Y H:i') ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $logs->withQueryString()->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <p class="text-gray-500">Zadne zaznamy{{ $selectedCity ? " pro mesto {$selectedCity}" : '' }}.</p>
            </div>
        @endif
    </div>
@endsection
