<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Počasí')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }

        .bg-mesh {
            background: linear-gradient(135deg, #0c1445 0%, #1a1a5e 25%, #0d2137 50%, #1b0f3a 75%, #0c1445 100%);
            background-size: 400% 400%;
            animation: meshMove 20s ease infinite;
        }
        @keyframes meshMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .glass {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-light {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .glass-nav {
            background: rgba(12, 20, 69, 0.6);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .weather-emoji { font-size: 3rem; line-height: 1; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3)); }
        .weather-emoji-lg { font-size: 5rem; line-height: 1; filter: drop-shadow(0 6px 16px rgba(0,0,0,0.4)); }

        .temp-display {
            background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.65) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glow-blue { box-shadow: 0 0 40px rgba(99, 102, 241, 0.12), 0 0 80px rgba(99, 102, 241, 0.05); }

        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.25);
        }

        .stars {
            position: fixed; inset: 0;
            pointer-events: none; z-index: 0;
            overflow: hidden;
        }
        .stars::before, .stars::after {
            content: '';
            position: absolute;
            width: 2px; height: 2px;
            border-radius: 50%;
            background: white;
            box-shadow:
                50px 100px 0 rgba(255,255,255,0.08),
                200px 50px 0 rgba(255,255,255,0.05),
                350px 200px 0 rgba(255,255,255,0.07),
                500px 80px 0 rgba(255,255,255,0.09),
                650px 300px 0 rgba(255,255,255,0.04),
                800px 150px 0 rgba(255,255,255,0.06),
                950px 250px 0 rgba(255,255,255,0.05),
                100px 400px 0 rgba(255,255,255,0.06),
                300px 350px 0 rgba(255,255,255,0.08),
                700px 400px 0 rgba(255,255,255,0.04),
                150px 250px 0 rgba(255,255,255,0.05),
                450px 150px 0 rgba(255,255,255,0.07),
                1100px 100px 0 rgba(255,255,255,0.05),
                1250px 300px 0 rgba(255,255,255,0.06),
                1400px 180px 0 rgba(255,255,255,0.04);
            animation: twinkle 5s infinite alternate;
        }
        .stars::after {
            box-shadow:
                120px 180px 0 rgba(255,255,255,0.06),
                380px 90px 0 rgba(255,255,255,0.05),
                550px 280px 0 rgba(255,255,255,0.08),
                720px 50px 0 rgba(255,255,255,0.04),
                880px 350px 0 rgba(255,255,255,0.06),
                1020px 220px 0 rgba(255,255,255,0.05),
                260px 320px 0 rgba(255,255,255,0.07),
                480px 420px 0 rgba(255,255,255,0.04);
            animation-delay: 2.5s;
        }
        @keyframes twinkle { from { opacity: 0.4; } to { opacity: 1; } }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeInUp 0.5s ease-out forwards; }
        .fade-d1 { animation-delay: 0.08s; opacity: 0; }
        .fade-d2 { animation-delay: 0.16s; opacity: 0; }
        .fade-d3 { animation-delay: 0.24s; opacity: 0; }
        .fade-d4 { animation-delay: 0.32s; opacity: 0; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body class="bg-mesh min-h-screen text-white antialiased">
    <div class="stars"></div>

    <nav class="glass-nav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-8">
                    <a href="{{ route('weather.dashboard') }}" class="flex items-center gap-2.5 group">
                        <span class="text-2xl group-hover:scale-110 transition-transform">🌤️</span>
                        <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-blue-200 to-purple-300 bg-clip-text text-transparent">
                            Počasí
                        </span>
                    </a>
                    <div class="hidden sm:flex items-center gap-1">
                        <a href="{{ route('weather.dashboard') }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('weather.dashboard') ? 'bg-white/10 text-white shadow-lg shadow-white/5' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('weather.history') }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('weather.history') ? 'bg-white/10 text-white shadow-lg shadow-white/5' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                            Historie
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-2 text-xs text-white/30">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-400/80"></span>
                    Open-Meteo &middot; Free API
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        @if(session('success'))
            <div class="mb-6 rounded-xl p-4 bg-emerald-500/10 border border-emerald-400/20 text-emerald-300 fade-in flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 rounded-xl p-4 bg-red-500/10 border border-red-400/20 text-red-300 fade-in flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="relative z-10 py-10 text-center text-white/15 text-xs tracking-wide">
        Zerops &middot; Open-Meteo.com
    </footer>
</body>
</html>
