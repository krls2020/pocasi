<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Weather Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('weather.dashboard') }}" class="text-xl font-bold text-blue-600">Weather Dashboard</a>
                    <a href="{{ route('weather.dashboard') }}" class="text-gray-600 hover:text-gray-900 {{ request()->routeIs('weather.dashboard') ? 'font-semibold text-gray-900' : '' }}">Dashboard</a>
                    <a href="{{ route('weather.history') }}" class="text-gray-600 hover:text-gray-900 {{ request()->routeIs('weather.history') ? 'font-semibold text-gray-900' : '' }}">Historie</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
