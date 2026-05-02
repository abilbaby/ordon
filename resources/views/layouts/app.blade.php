<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    @php
        $menuByRole = [
            'admin' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
                ['label' => 'Donors', 'route' => 'admin.donors', 'active' => 'admin.donors'],
                ['label' => 'Recipients', 'route' => 'admin.recipients', 'active' => 'admin.recipients'],
                ['label' => 'Flagged Recipients', 'route' => 'admin.flagged-recipients', 'active' => 'admin.flagged-recipients'],
                ['label' => 'Matches', 'route' => 'admin.matches', 'active' => 'admin.matches'],
                ['label' => 'Hospitals', 'route' => 'admin.hospitals', 'active' => 'admin.hospitals'],
                ['label' => 'Doctors', 'route' => 'admin.doctors', 'active' => 'admin.doctors'],
                ['label' => 'Organs', 'route' => 'admin.organs', 'active' => 'admin.organs'],
                ['label' => 'Reports', 'route' => 'admin.reports', 'active' => 'admin.reports'],
                ['label' => 'Donation Logs', 'route' => 'admin.donation-logs', 'active' => 'admin.donation-logs'],
                ['label' => 'Notifications', 'route' => 'notifications.index', 'active' => 'notifications.*'],
                ['label' => 'Settings', 'route' => 'admin.settings', 'active' => 'admin.settings*'],
            ],
            'donor' => [
                ['label' => 'Dashboard', 'route' => 'donor.dashboard', 'active' => 'donor.dashboard'],
                ['label' => 'My Matches', 'route' => 'donor.matches', 'active' => 'donor.matches'],
                ['label' => 'Certificate', 'route' => 'donor.certificate', 'active' => 'donor.certificate'],
                ['label' => 'Notifications', 'route' => 'notifications.index', 'active' => 'notifications.*'],
            ],
            'recipient' => [
                ['label' => 'Dashboard', 'route' => 'recipient.dashboard', 'active' => 'recipient.dashboard'],
                ['label' => 'Profile', 'route' => 'recipient.profile', 'active' => 'recipient.profile*'],
                ['label' => 'My Request', 'route' => 'recipient.requests', 'active' => 'recipient.requests'],
                ['label' => 'Match Status', 'route' => 'recipient.matches', 'active' => 'recipient.matches'],
                ['label' => 'Edit Profile', 'route' => 'recipient.edit-profile', 'active' => 'recipient.edit-profile'],
                ['label' => 'Notifications', 'route' => 'notifications.index', 'active' => 'notifications.*'],
            ],
            'hospital' => [
                ['label' => 'Dashboard', 'route' => 'hospital.dashboard', 'active' => 'hospital.dashboard'],
                ['label' => 'Approvals', 'route' => 'hospital.approvals', 'active' => 'hospital.approvals'],
                ['label' => 'Transplants', 'route' => 'hospital.transplants', 'active' => 'hospital.transplants'],
                ['label' => 'Planner', 'route' => 'hospital.planner', 'active' => 'hospital.planner'],
                ['label' => 'Notifications', 'route' => 'notifications.index', 'active' => 'notifications.*'],
            ],
        ];
        $menu = $menuByRole[auth()->user()->role] ?? [];
        $role = auth()->user()->role;
        $opsAlerts = match ($role) {
            'admin' => \App\Models\Donor::where('fraud_flag', true)->count()
                + \App\Models\Hospital::where('fraud_flag', true)->count()
                + \App\Models\Recipient::where('priority_escalation_requested', true)->count(),
            'donor' => \App\Models\EmergencyRequest::where('status', 'open')
                ->where('blood_group', optional(auth()->user()->donor)->blood_group)
                ->count(),
            'recipient' => \App\Models\AllocationMatch::whereHas('recipient', fn ($q) => $q->where('user_id', auth()->id()))
                ->whereIn('status', ['MATCHED', 'APPROVED'])
                ->count(),
            'hospital' => \App\Models\AllocationMatch::where('status', 'MATCHED')->count(),
            default => 0,
        };
        $accountStatus = match ($role) {
            'donor' => optional(auth()->user()->donor) &&
                auth()->user()->donor->identity_verified &&
                auth()->user()->donor->approved &&
                auth()->user()->donor->medical_status === 'VERIFIED'
                    ? 'Approved'
                    : 'Pending Approval',
            'recipient' => optional(auth()->user()->recipient) &&
                auth()->user()->recipient->admin_approved
                    ? 'Approved'
                    : 'Pending Approval',
            'hospital' => optional(auth()->user()->hospital) &&
                auth()->user()->hospital->identity_verified &&
                auth()->user()->hospital->approved
                    ? 'Approved'
                    : 'Pending Approval',
            default => null,
        };
        $unreadNotifications = \App\Models\UserNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    @endphp
    <header class="lg:hidden bg-white shadow-sm border-b border-slate-200 px-4 py-3">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/ordon-logo.png') }}" alt="ORDON logo" class="h-9 w-auto" />
                <span class="text-base font-semibold text-slate-800">ORDON</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-medium text-white">
                    Logout
                </button>
            </form>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <h1 class="text-xl font-semibold text-slate-800">ORDON Dashboard</h1>
            @if($accountStatus)
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $accountStatus === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $accountStatus }}
                </span>
            @endif
        </div>
        <nav class="mt-3 flex gap-2 overflow-x-auto pb-1">
            @foreach ($menu as $item)
                <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                    class="whitespace-nowrap rounded-lg px-3 py-2 text-xs font-medium {{ request()->routeIs($item['active']) ? 'bg-cyan-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </header>
    <div class="min-h-screen flex">
        <aside class="hidden lg:flex w-[270px] bg-[#0b3650] text-slate-100 fixed inset-y-0 left-0 p-6 border-r border-[#124a69] flex-col h-screen overflow-hidden">
            <a href="{{ route('dashboard') }}" class="mb-8 flex items-center gap-3 shrink-0">
                <img src="{{ asset('images/ordon-logo.png') }}" alt="ORDON logo" class="h-12 w-auto bg-white rounded-xl p-1" />
            </a>
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-x-0 top-0 h-6 bg-gradient-to-b from-[#0b3650] to-transparent z-10"></div>
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-6 bg-gradient-to-t from-[#0b3650] to-transparent z-10"></div>
                <div class="h-full overflow-y-auto pr-1 [scrollbar-width:thin] [scrollbar-color:#2b6a8f_transparent]">
                <nav class="space-y-2">
                   @foreach ($menu as $item)
                        <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                            class="block rounded-xl px-4 py-3 transition-all duration-200 fade-in {{ request()->routeIs($item['active']) ? 'border-l-4 border-cyan-300 bg-[#125273] text-white' : 'hover:bg-[#125273] text-slate-200' }}">
                            {{ $item['label'] }}
                        </a>  
                    @endforeach
                    
                </nav>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-[#1b5b7f] text-xs text-slate-300 shrink-0">
                <a href="{{ url('/') }}" class="block py-1 hover:text-white">Public Home</a>
                <a href="{{ route('contact') }}" class="block py-1 hover:text-white">Contact Support</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button class="w-full text-left py-1 text-rose-200 hover:text-rose-100">Log Out</button>
                </form>
            </div>
        </aside>

        <div class="min-w-0 flex-1 lg:ml-[270px]">
            <header class="hidden lg:flex bg-white/95 backdrop-blur border-b border-slate-200 px-8 py-4 items-center justify-between sticky top-0 z-20">
                <div class="min-w-0 flex items-center gap-3">
                    <button type="button" onclick="window.history.length > 1 ? history.back() : window.location.assign('{{ route('dashboard') }}')"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">
                        Back
                    </button>
                    <h2 class="truncate text-2xl font-semibold text-slate-900">{{ $title ?? 'Dashboard' }}</h2>
                </div>
                <div class="flex min-w-0 items-center gap-3">
                    <div class="rounded-xl bg-slate-100 px-3 py-2 text-xs text-slate-700">
                        Ops Alerts: <span class="font-semibold">{{ $opsAlerts }}</span>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="rounded-xl bg-slate-100 px-3 py-2 text-xs text-slate-700 hover:bg-slate-200">
                        Notifications: <span class="font-semibold">{{ $unreadNotifications }}</span>
                    </a>
                    <span class="rounded-xl bg-[#e8f4fb] text-[#0b3650] px-3 py-2 text-xs uppercase">{{ $role }}</span>
                    @if ($accountStatus)
                        <span class="rounded-xl px-3 py-2 text-xs {{ $accountStatus === 'Approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">
                            Account: {{ $accountStatus }}
                        </span>
                    @endif
                    <span class="max-w-48 truncate rounded-xl bg-slate-100 px-4 py-2 text-sm">{{ auth()->user()->name ?? 'User' }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="p-4 sm:p-6 fade-in-up">
                @if (session('success'))
                    <div class="mb-4 rounded-2xl bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-2xl bg-rose-100 text-rose-800 px-4 py-3">{{ session('error') }}</div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
