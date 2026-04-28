<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            @keyframes pulse-glow {
                0%, 100% { box-shadow: 0 0 20px rgba(11, 110, 162, 0.1); }
                50% { box-shadow: 0 0 40px rgba(11, 110, 162, 0.2); }
            }
            .animate-float { animation: float 6s ease-in-out infinite; }
            .animate-pulse-glow { animation: pulse-glow 4s ease-in-out infinite; }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <!-- Animated Background -->
        <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-cyan-50">
            <!-- Floating Shapes -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-20 left-10 w-64 h-64 bg-cyan-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 0s;"></div>
                <div class="absolute top-40 right-20 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 2s;"></div>
                <div class="absolute bottom-20 left-1/3 w-72 h-72 bg-slate-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 4s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMtOS45NDEgMC0xOCA4LjA1OS0xOCAxOHM4LjA1OSAxOCAxOCAxOCAxOC04LjA1OSAxOC0xOC04LjA1OS0xOC0xOC0xOHptMCAzMmMtNy43MzIgMC0xNC02LjI2OC0xNC0xNHM2LjI2OC0xNCAxNC0xNCAxNCA2LjI2OCAxNCAxNC02LjI2OCAxNC0xNCAxNHoiIGZpbGw9IiMwMDAiIGZpbGwtb3BhY2l0eT0iLjAyIi8+PC9nPjwvc3ZnPg')] opacity-50"></div>

            <div class="min-h-screen flex flex-col justify-center items-center px-4 relative z-10">
                <div class="w-full sm:max-w-md flex items-center justify-between mb-8">
                    <a href="/" class="flex items-center gap-2 animate-pulse-glow rounded-2xl p-2 bg-white/80 backdrop-blur">
                        <img src="{{ asset('images/ordon-logo.png') }}" class="h-14 w-auto" alt="ORDON logo" />
                    </a>
                    <button type="button"
                        onclick="window.history.length > 1 ? history.back() : window.location.assign('/')"
                        class="rounded-xl border border-slate-200 bg-white/80 backdrop-blur px-4 py-2 text-sm text-slate-700 hover:bg-white hover:shadow-md transition-all duration-300">
                        🏠 Home
                    </button>
                </div>

                <div class="w-full sm:max-w-md px-8 py-8 bg-white/80 backdrop-blur-md shadow-xl overflow-hidden rounded-3xl border border-white/50">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
