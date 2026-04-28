<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us | ORDON</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 30px rgba(11, 110, 162, 0.15); }
            50% { box-shadow: 0 0 50px rgba(11, 110, 162, 0.25); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 4s ease-in-out infinite; }
        .input-animate { transition: all 0.3s ease; }
        .input-animate:focus { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(11, 110, 162, 0.15); }
        .btn-animate { transition: all 0.3s ease; position: relative; overflow: hidden; }
        .btn-animate:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(11, 110, 162, 0.35); }
        .card-hover { transition: all 0.4s ease; }
        .card-hover:hover { transform: translateY(-8px); }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-cyan-50 text-slate-900 font-sans antialiased">
    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-slate-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 4s;"></div>
    </div>

    <header class="relative z-10 bg-white/80 backdrop-blur-md border-b border-white/50 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 animate-pulse-glow rounded-xl p-2 bg-white">
                <img src="{{ asset('images/ordon-logo.png') }}" class="h-12 w-auto rounded-xl" alt="ORDON logo">
                <span class="font-bold text-xl text-gradient bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">ORDON</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="/" class="text-sm text-slate-600 hover:text-cyan-600 transition-colors">Home</a>
                <button type="button" onclick="window.history.length > 1 ? history.back() : window.location.assign('/')"
                    class="rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700 hover:bg-white hover:shadow-md transition-all duration-300">
                    Back
                </button>
            </div>
        </div>
    </header>

    <main class="relative z-10 max-w-6xl mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-3xl mb-6 shadow-lg animate-float">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-slate-900 mb-3">Get in Touch</h1>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">We'd love to hear from you. Reach out for platform support, hospital onboarding, or organ donation information.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Contact Info Card -->
            <div class="card-hover bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 p-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-cyan-100 rounded-xl">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    Contact Information
                </h2>
                <div class="space-y-6">
                    <div class="flex items-start gap-4 p-4 bg-gradient-to-r from-cyan-50 to-blue-50 rounded-2xl border border-cyan-100">
                        <div class="flex-shrink-0 w-12 h-12 bg-cyan-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Email</p>
                            <p class="font-semibold text-slate-900">support@ordon.org</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-100">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Phone</p>
                            <p class="font-semibold text-slate-900">+880 1234 567890</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Address</p>
                            <p class="font-semibold text-slate-900">Kerala, India</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl border border-purple-100">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Office Hours</p>
                            <p class="font-semibold text-slate-900">Sun-Thu, 9:00 AM - 5:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form Card -->
            <div class="card-hover bg-white/80 backdrop-blur-md rounded-3xl shadow-xl border border-white/50 p-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </span>
                    Send a Message
                </h2>
                <form class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Your Name</label>
                        <input class="input-animate w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 bg-white/80" placeholder="Enter your name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                        <input class="input-animate w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 bg-white/80" placeholder="Enter your email">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subject</label>
                        <input class="input-animate w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 bg-white/80" placeholder="What is this about?">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                        <textarea class="input-animate w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 bg-white/80" rows="5" placeholder="Write your message..."></textarea>
                    </div>
                    <button type="button" class="btn-animate w-full py-4 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-xl flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
