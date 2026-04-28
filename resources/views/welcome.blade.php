<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ORDON | Organ Donation and Transplant Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">
    @php
        $ctaRoute = Route::has('register') ? route('register') : route('login');
    @endphp

    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur" x-data="{ mobileMenu: false }">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('images/ordon-logo.png') }}" class="h-11 w-auto rounded-xl" alt="ORDON logo">
                <div class="min-w-0">
                    <p class="font-semibold tracking-wide text-slate-950">ORDON</p>
                    <p class="hidden text-xs text-slate-500 sm:block">Organ Donation and Transplant Platform</p>
                </div>
            </a>

            <nav class="hidden items-center gap-7 text-sm font-medium text-slate-600 md:flex">
                <a href="#features" class="hover:text-cyan-700">Features</a>
                <a href="#process" class="hover:text-cyan-700">How it works</a>
                <a href="#stories" class="hover:text-cyan-700">Stories</a>
                <a href="#faq" class="hover:text-cyan-700">FAQ</a>
                <a href="{{ route('contact') }}" class="hover:text-cyan-700">Contact</a>
            </nav>

            <div class="flex items-center gap-2">
                <button type="button" @click="mobileMenu = ! mobileMenu" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 md:hidden">
                    Menu
                </button>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 sm:inline-flex">Login</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Register</a>
                @endauth
            </div>
        </div>

        <div x-show="mobileMenu" x-transition class="border-t border-slate-200 bg-white px-4 py-3 md:hidden">
            <nav class="flex flex-col gap-1 text-sm font-medium text-slate-700">
                <a @click="mobileMenu = false" href="#features" class="rounded-xl px-3 py-2 hover:bg-slate-100">Features</a>
                <a @click="mobileMenu = false" href="#process" class="rounded-xl px-3 py-2 hover:bg-slate-100">How it works</a>
                <a @click="mobileMenu = false" href="#stories" class="rounded-xl px-3 py-2 hover:bg-slate-100">Stories</a>
                <a @click="mobileMenu = false" href="#faq" class="rounded-xl px-3 py-2 hover:bg-slate-100">FAQ</a>
                <a @click="mobileMenu = false" href="{{ route('contact') }}" class="rounded-xl px-3 py-2 hover:bg-slate-100">Contact</a>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="sm:hidden">
                        @csrf
                        <button class="w-full rounded-xl px-3 py-2 text-left text-rose-700 hover:bg-rose-50">Logout</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden bg-white">
            <div class="absolute inset-x-0 top-0 h-full bg-[linear-gradient(180deg,#ecfeff_0%,#ffffff_46%,#f8fafc_100%)]"></div>
            <div class="relative mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-2 lg:px-8 lg:py-24">
                <div>
                    <p class="inline-flex rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-700">
                        Trusted transplant coordination
                    </p>
                    <h1 class="mt-6 text-4xl font-bold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                        Coordinate organ donation with clarity, speed, and trust.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        ORDON helps donors, recipients, and hospitals manage verified registration, fair matching, and transplant workflows from one secure platform.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ $ctaRoute }}" class="inline-flex justify-center rounded-xl bg-cyan-700 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-cyan-800">
                            Become a Donor
                        </a>
                        <a href="{{ $ctaRoute }}" class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-100">
                            Request a Transplant
                        </a>
                    </div>

                    <div class="mt-8 grid gap-3 text-sm text-slate-700 sm:grid-cols-3">
                        @foreach (['Secure identity verification', 'Hospital-approved workflows', 'Real-time tracking'] as $indicator)
                            <div class="flex items-center gap-2">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.313a1 1 0 0 1-1.42 0L3.29 9.267a1 1 0 1 1 1.42-1.414l4.04 4.04 6.54-6.596a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span>{{ $indicator }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="relative">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <img src="{{ asset('images/ordon-landing-hero.png') }}" alt="Organ donation awareness" class="mx-auto w-full max-w-md rounded-xl object-cover">
                        <div class="mt-6 border-t border-slate-200 pt-5">
                            <h3 class="text-center text-xl font-semibold text-slate-950">Why Organ Donation Matters</h3>
                            <p class="mx-auto mt-2 max-w-md text-center text-sm leading-6 text-slate-600">
                                A single donor can save multiple lives through timely and safe transplantation. Early registration, family awareness, and hospital guidance can reduce delays for patients in critical need.
                            </p>
                            <div class="mt-5 flex flex-wrap justify-center gap-2">
                                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">One donor can save lives</span>
                                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">Family consent matters</span>
                                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">Medical guidance first</span>
                            </div>
                            <div class="mt-5 grid grid-cols-1 gap-3 text-center sm:grid-cols-3">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <p class="text-xs text-slate-500">Potential Lives Helped</p>
                                    <p class="mt-1 text-lg font-bold text-slate-950">Up to 8</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <p class="text-xs text-slate-500">Impact Begins With</p>
                                    <p class="mt-1 text-lg font-bold text-slate-950">Registration</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <p class="text-xs text-slate-500">Best Time to Register</p>
                                    <p class="mt-1 text-lg font-bold text-slate-950">Today</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-y border-slate-200 bg-slate-100/70">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Platform Impact</p>
                        <h2 class="mt-2 text-2xl font-bold text-slate-950">Trusted records across the transplant network</h2>
                    </div>
                    <p class="text-sm text-slate-500">Clear visibility across donors, recipients, hospitals, and donated organs.</p>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-landing.stat-card value="42" label="Organs Donated" detail="Verified donor-organ records" />
                    <x-landing.stat-card value="128" label="Recipients" detail="Registered recipient profiles" />
                    <x-landing.stat-card value="96" label="Donors" detail="Available donor profiles" />
                    <x-landing.stat-card value="18" label="Hospitals" detail="Connected hospital accounts" />
                </div>
            </div>
        </section>

        <section id="features" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <x-landing.section-heading
                eyebrow="Platform Features"
                title="Everything teams need to manage transplant workflows responsibly."
                description="ORDON replaces disconnected updates with verified records, clear approvals, and transparent progress tracking."
                align="center"
            />

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <x-landing.feature-card title="Smart Matching Engine" description="Prioritizes compatibility, urgency, waiting time, and clinical context for fairer shortlists.">
                    <x-slot:icon>
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h10M4 17h10M17 5l3 2-3 2M17 15l3 2-3 2M14 7c2 0 3 2 5 2M14 17c2 0 3-2 5-2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </x-slot:icon>
                </x-landing.feature-card>
                <x-landing.feature-card title="Secure Identity Verification" description="Keeps donor and recipient records cleaner with verified IDs and approval checkpoints.">
                    <x-slot:icon>
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3 5 6v5c0 4.5 2.9 8.5 7 10 4.1-1.5 7-5.5 7-10V6l-7-3Z" stroke-linecap="round" stroke-linejoin="round"/><path d="m9 12 2 2 4-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </x-slot:icon>
                </x-landing.feature-card>
                <x-landing.feature-card title="Hospital Coordination" description="Centralizes review, approval, transplant scheduling, and completion updates for care teams.">
                    <x-slot:icon>
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 21V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v16M3 21h18M8 8h5M8 12h5M8 16h2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </x-slot:icon>
                </x-landing.feature-card>
                <x-landing.feature-card title="Real-Time Status Tracking" description="Gives every stakeholder a clear view of registration, verification, matching, and approval status.">
                    <x-slot:icon>
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15l3-4 3 2 5-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </x-slot:icon>
                </x-landing.feature-card>
            </div>
        </section>

        <section id="process" class="bg-white">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <x-landing.section-heading
                    eyebrow="How it works"
                    title="A clear path from registration to transplant execution."
                    description="Each stage is designed to reduce confusion while preserving medical review and accountability."
                />

                <div class="mt-10 grid grid-cols-1 gap-4 lg:grid-cols-4">
                    @foreach ([
                        ['Register', 'Donors and recipients enter structured profiles or hospital-issued invitations.'],
                        ['Verification', 'Identity, eligibility, and clinical details move through approval checks.'],
                        ['Matching Process', 'The system ranks possible matches using compatibility and urgency signals.'],
                        ['Transplant Execution', 'Hospitals validate, schedule, complete, and document the transplant workflow.'],
                    ] as [$title, $description])
                        <div class="relative rounded-xl border border-slate-200 bg-slate-50 p-6">
                            <div class="mb-5 flex h-10 w-10 items-center justify-center rounded-full bg-slate-950 text-sm font-bold text-white">
                                {{ $loop->iteration }}
                            </div>
                            <h3 class="font-semibold text-slate-950">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="stories" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <x-landing.section-heading
                eyebrow="Success Stories"
                title="Better coordination creates better outcomes."
                description="Short examples of how clear workflows help families, hospitals, and transplant teams move with confidence."
                align="center"
            />

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <x-landing.story-card initials="RC" title="Recipient Care Coordinated" description="A recipient profile was verified, reviewed by the hospital, and moved into matching with clear status updates for the care team." />
                <x-landing.story-card initials="DR" title="Donor Records Verified" description="A donor completed registration with identity checks and organ availability details, helping hospitals review suitable cases faster." />
                <x-landing.story-card initials="HT" title="Hospital Teams Aligned" description="Hospital staff tracked approvals, match decisions, and transplant readiness from one shared workflow instead of scattered follow-ups." />
            </div>
        </section>

        <section id="faq" class="bg-white">
            <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
                <x-landing.section-heading
                    eyebrow="FAQ"
                    title="Questions people ask before getting started."
                    description="Simple answers for donors, recipients, and hospital teams evaluating ORDON."
                    align="center"
                />

                <div class="mt-10 space-y-3" x-data="{ open: 1 }">
                    <x-landing.faq-item index="1" question="Who can register on ORDON?">
                        Donors can register directly. Recipient registration may be managed through hospital workflows depending on verification needs. Hospital and admin access is reviewed before approval.
                    </x-landing.faq-item>
                    <x-landing.faq-item index="2" question="How are organ matches prioritized?">
                        ORDON considers blood compatibility, urgency, waiting time, and clinical status signals to help care teams review matches responsibly.
                    </x-landing.faq-item>
                    <x-landing.faq-item index="3" question="Can recipients track their transplant progress?">
                        Yes. Recipients can follow their status from registration through verification, matching, approval, and completion inside the dashboard.
                    </x-landing.faq-item>
                    <x-landing.faq-item index="4" question="Are hospital approvals required before transplant?">
                        Yes. Hospital review and approval remain part of the workflow before transplant scheduling and completion are recorded.
                    </x-landing.faq-item>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-slate-950 px-6 py-12 text-center text-white shadow-sm sm:px-10">
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-200">Start today</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Ready to Save a Life?</h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-slate-300">
                    Join a verified transplant workflow built for donors, recipients, and hospitals that need clarity when every step matters.
                </p>
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ $ctaRoute }}" class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-950 hover:bg-slate-100">Become a Donor</a>
                    <a href="{{ $ctaRoute }}" class="rounded-xl border border-white/20 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10">Request a Transplant</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 py-12 sm:px-6 md:grid-cols-4 lg:px-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/ordon-logo.png') }}" class="h-11 w-auto rounded-xl" alt="ORDON logo">
                    <p class="font-semibold text-slate-950">ORDON</p>
                </div>
                <p class="mt-4 max-w-md text-sm leading-6 text-slate-600">
                    ORDON is a secure organ donation and transplant coordination platform for verified registration, hospital approvals, fair matching, and status visibility.
                </p>
                <div class="mt-5 flex gap-3">
                    <a href="#" aria-label="ORDON on LinkedIn" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-100">in</a>
                    <a href="#" aria-label="ORDON on X" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-100">X</a>
                    <a href="#" aria-label="ORDON on Facebook" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-100">f</a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-slate-950">Quick Links</h4>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li><a href="#features" class="hover:text-cyan-700">Features</a></li>
                    <li><a href="#process" class="hover:text-cyan-700">How it works</a></li>
                    <li><a href="#stories" class="hover:text-cyan-700">Success Stories</a></li>
                    <li><a href="#faq" class="hover:text-cyan-700">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-slate-950">Contact</h4>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li><a href="{{ route('contact') }}" class="hover:text-cyan-700">Contact support</a></li>
                    <li>support@ordon.org</li>
                    <li>+880 1234 567890</li>
                    <li>Kerala, India</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-200 px-4 py-5 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} ORDON. All rights reserved.
        </div>
    </footer>
</body>
</html>
