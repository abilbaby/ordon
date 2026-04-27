<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access Limited | {{ config('app.name', 'ORDON') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-cyan-50 to-sky-100 text-slate-900 antialiased">
    <main class="min-h-screen flex items-center justify-center px-4 py-10">
        <section class="w-full max-w-2xl rounded-3xl border border-[#d7e8f4] bg-white/95 backdrop-blur shadow-xl p-8 md:p-10">
            <div class="flex items-center gap-3 mb-6">
                <img src="{{ asset('images/ordon-logo.png') }}" alt="ORDON logo" class="h-12 w-auto rounded-xl bg-white p-1 border border-slate-200">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">ORDON Access Notice</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Thank you for your patience</h1>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 mb-5">
                <p class="text-sm text-amber-900 font-medium">This area is currently restricted for your account.</p>
            </div>

            <p class="text-slate-600 leading-relaxed">
                We appreciate your interest in supporting safe organ allocation. Your account is likely pending admin verification or this section is reserved for another role.
            </p>

            <p class="text-slate-600 leading-relaxed mt-3">
                Thank you for helping keep ORDON secure and trusted for donors, recipients, and hospitals.
            </p>

            @if (!empty($exception) && $exception->getMessage())
                <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Details</p>
                    <p class="text-sm text-slate-700">{{ $exception->getMessage() }}</p>
                </div>
            @endif

            <div class="mt-7 flex flex-wrap gap-3">
                <button type="button" onclick="window.history.length > 1 ? history.back() : window.location.assign('{{ url('/') }}')"
                    class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-all">
                    Back
                </button>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-xl bg-slate-900 text-white px-4 py-2.5 text-sm font-semibold hover:bg-slate-800 transition-all">Go to Dashboard</a>
                <a href="{{ url('/') }}" class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-all">Back to Home</a>
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-sky-300 px-4 py-2.5 text-sm font-semibold text-sky-700 hover:bg-sky-50 transition-all">Login</a>
                @endguest
            </div>
        </section>
    </main>
</body>
</html>
