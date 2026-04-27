<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us | ORDON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#eef7fc] text-slate-900 font-sans antialiased">
    <header class="bg-white border-b border-[#d7e8f4]">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/ordon-logo.png') }}" class="h-12 w-auto rounded-xl" alt="ORDON logo">
                <span class="font-semibold text-[#0b3650]">ORDON</span>
            </a>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.history.length > 1 ? history.back() : window.location.assign('/')"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-all duration-200">
                    Back
                </button>
                <a href="/" class="text-sm text-[#0b6ea2] hover:underline">Back to Home</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-md border border-[#d7e8f4] p-6">
                <h1 class="text-3xl font-semibold text-[#0b3650] mb-3">Contact Us</h1>
                <p class="text-slate-600 mb-6">Reach out for platform support, hospital onboarding, or organ donation information.</p>
                <div class="space-y-2 text-slate-700">
                    <p><strong>Email:</strong> support@ordon.org</p>
                    <p><strong>Phone:</strong> +880 1234 567890</p>
                    <p><strong>Address:</strong> Dhaka, Bangladesh</p>
                    <p><strong>Office Hours:</strong> Sun-Thu, 9:00 AM - 5:00 PM</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md border border-[#d7e8f4] p-6">
                <h2 class="text-xl font-semibold text-[#0b3650] mb-4">Send a Message</h2>
                <form class="space-y-4">
                    <input class="w-full rounded-xl border-[#c8dfef]" placeholder="Your name">
                    <input class="w-full rounded-xl border-[#c8dfef]" placeholder="Email address">
                    <input class="w-full rounded-xl border-[#c8dfef]" placeholder="Subject">
                    <textarea class="w-full rounded-xl border-[#c8dfef]" rows="4" placeholder="Write your message"></textarea>
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-[#0b6ea2] text-white font-medium">Submit</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
