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
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-100">
        <div class="min-h-screen flex flex-col justify-center items-center px-4">
            <div class="w-full sm:max-w-md flex items-center justify-between mb-5">
                <a href="/" class="flex items-center gap-2">
                    <img src="{{ asset('images/ordon-logo.png') }}" class="h-14 w-auto bg-white rounded-xl p-1" alt="ORDON logo" />
                </a>
                <button type="button"
                    onclick="window.history.length > 1 ? history.back() : window.location.assign('/')"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-all duration-200">
                    Home
                </button>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-md overflow-hidden rounded-2xl border border-slate-200">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
