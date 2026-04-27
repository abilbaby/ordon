<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    <div class="card-pro mb-6" x-data="{ open: localStorage.getItem('admin_flow_model') !== '0' }" x-init="$watch('open', v => localStorage.setItem('admin_flow_model', v ? '1' : '0'))">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Live Allocation Flow Model</h3>
            <div class="flex items-center gap-2">
                <span class="text-xs rounded-full bg-cyan-100 text-cyan-700 px-3 py-1">Real-time Governance View</span>
                <button type="button" @click="open = !open" class="text-xs rounded-lg border border-slate-300 px-2 py-1 text-slate-600 hover:bg-slate-100" x-text="open ? 'Collapse' : 'Expand'"></button>
            </div>
        </div>
        <div x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-5 gap-3 text-center text-sm">
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">
                <p class="text-slate-500">Intake</p>
                <p class="font-semibold">Registered</p>
                <p class="text-xs text-slate-500 mt-1">{{ $workflowStats['registered'] }} cases</p>
            </div>
            <div class="hidden md:flex items-center justify-center text-slate-400">→</div>
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">
                <p class="text-slate-500">Verification</p>
                <p class="font-semibold">Verified</p>
                <p class="text-xs text-slate-500 mt-1">{{ $workflowStats['verified'] }} cases</p>
            </div>
            <div class="hidden md:flex items-center justify-center text-slate-400">→</div>
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">
                <p class="text-slate-500">Allocation</p>
                <p class="font-semibold">Matched</p>
                <p class="text-xs text-slate-500 mt-1">{{ $workflowStats['matched'] }} cases</p>
            </div>
            <div class="hidden md:flex items-center justify-center text-slate-400">→</div>
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">
                <p class="text-slate-500">Clinical Decision</p>
                <p class="font-semibold">Approved</p>
                <p class="text-xs text-slate-500 mt-1">{{ $workflowStats['approved'] }} cases</p>
            </div>
            <div class="hidden md:flex items-center justify-center text-slate-400">→</div>
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3">
                <p class="text-emerald-700">Outcome</p>
                <p class="font-semibold text-emerald-800">Completed</p>
                <p class="text-xs text-emerald-700 mt-1">{{ $workflowStats['completed'] }} cases</p>
            </div>
        </div>
    </div>

    <div class="mb-6 flex justify-end">
        <form method="POST" action="{{ route('admin.auto-matching') }}">
            @csrf
            <button class="rounded-2xl bg-[#0b6ea2] text-white px-5 py-2.5 hover:bg-[#0a5f8b] transition-all duration-200">
                Run Auto Matching
            </button>
        </form>
    </div>

    <div class="card-pro mb-6" x-data="{ open: localStorage.getItem('admin_quick_panel') !== '0' }" x-init="$watch('open', v => localStorage.setItem('admin_quick_panel', v ? '1' : '0'))">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Operations Quick Panel</h3>
            <button type="button" @click="open = !open" class="text-xs rounded-lg border border-slate-300 px-2 py-1 text-slate-600 hover:bg-slate-100" x-text="open ? 'Collapse' : 'Expand'"></button>
        </div>
        <div x-show="open" x-transition class="grid md:grid-cols-4 gap-3">
            <a href="{{ route('admin.donors', ['status' => 'REGISTERED']) }}" class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3 hover:bg-white transition-all">
                <p class="text-xs text-slate-500">Action</p>
                <p class="font-semibold text-slate-800">Review New Donors</p>
            </a>
            <a href="{{ route('admin.hospitals', ['approved' => 'no']) }}" class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3 hover:bg-white transition-all">
                <p class="text-xs text-slate-500">Action</p>
                <p class="font-semibold text-slate-800">Pending Hospitals</p>
            </a>
            <a href="{{ route('admin.flagged-recipients') }}" class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3 hover:bg-white transition-all">
                <p class="text-xs text-slate-500">Action</p>
                <p class="font-semibold text-slate-800">Flagged Recipients</p>
            </a>
            <a href="{{ route('admin.reports') }}" class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3 hover:bg-white transition-all">
                <p class="text-xs text-slate-500">Action</p>
                <p class="font-semibold text-slate-800">Open Analytics Hub</p>
            </a>
        </div>
    </div>

    <div class="card-pro mb-6">
        <h3 class="text-lg font-semibold mb-3">Verification Queue</h3>
        <div class="flex flex-wrap gap-3 text-sm">
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800">Donor Pending: {{ $pendingVerification['donors'] }}</span>
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800">Recipient Pending: {{ $pendingVerification['recipients'] }}</span>
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800">Hospital Pending: {{ $pendingVerification['hospitals'] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
        <a href="{{ route('admin.donors') }}" class="card-pro block hover:ring-2 hover:ring-[#0b6ea2]/20">
            <p class="text-slate-500">Total Donors</p>
            <p class="text-3xl font-semibold mt-2">{{ $totalDonors }}</p>
        </a>
        <a href="{{ route('admin.recipients') }}" class="card-pro block hover:ring-2 hover:ring-[#0b6ea2]/20">
            <p class="text-slate-500">Total Recipients</p>
            <p class="text-3xl font-semibold mt-2">{{ $totalRecipients }}</p>
        </a>
        <a href="{{ route('admin.matches', ['status' => 'MATCHED']) }}" class="card-pro block hover:ring-2 hover:ring-[#0b6ea2]/20">
            <p class="text-slate-500">Active Matches</p>
            <p class="text-3xl font-semibold mt-2">{{ $activeMatches }}</p>
        </a>
        <a href="{{ route('admin.matches', ['status' => 'APPROVED']) }}" class="card-pro block hover:ring-2 hover:ring-[#0b6ea2]/20">
            <p class="text-slate-500">Pending Approvals</p>
            <p class="text-3xl font-semibold mt-2">{{ $pendingApprovals }}</p>
        </a>
        <a href="{{ route('admin.donors', ['fraud' => 'yes']) }}" class="card-pro block hover:ring-2 hover:ring-[#0b6ea2]/20">
            <p class="text-slate-500">Flagged Donors</p>
            <p class="text-3xl font-semibold mt-2">{{ $flaggedDonors }}</p>
        </a>
        <a href="{{ route('admin.hospitals', ['fraud' => 'yes']) }}" class="card-pro block hover:ring-2 hover:ring-[#0b6ea2]/20">
            <p class="text-slate-500">Flagged Hospitals</p>
            <p class="text-3xl font-semibold mt-2">{{ $flaggedHospitals }}</p>
        </a>
        <a href="{{ route('admin.blacklist-registry') }}" class="card-pro block hover:ring-2 hover:ring-[#0b6ea2]/20">
            <p class="text-slate-500">Blacklist Count</p>
            <p class="text-3xl font-semibold mt-2">{{ $blacklistCount }}</p>
        </a>
    </div>

    <div class="card-pro">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Recent Matches</h3>
            <form method="GET" class="flex items-center gap-2">
                <select name="status" class="rounded-xl border-slate-200 text-sm">
                    <option value="" @selected($selectedStatus === '')>All statuses</option>
                    @foreach (['MATCHED', 'APPROVED', 'COMPLETED', 'REJECTED'] as $status)
                        <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm">Filter</button>
            </form>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
            <div class="rounded-xl bg-slate-100 p-3 text-center"><p class="text-xs text-slate-500">Registered</p><p class="font-semibold">{{ $workflowStats['registered'] }}</p></div>
            <div class="rounded-xl bg-slate-100 p-3 text-center"><p class="text-xs text-slate-500">Verified</p><p class="font-semibold">{{ $workflowStats['verified'] }}</p></div>
            <div class="rounded-xl bg-slate-100 p-3 text-center"><p class="text-xs text-slate-500">Matched</p><p class="font-semibold">{{ $workflowStats['matched'] }}</p></div>
            <div class="rounded-xl bg-slate-100 p-3 text-center"><p class="text-xs text-slate-500">Approved</p><p class="font-semibold">{{ $workflowStats['approved'] }}</p></div>
            <div class="rounded-xl bg-slate-100 p-3 text-center"><p class="text-xs text-slate-500">Completed</p><p class="font-semibold">{{ $workflowStats['completed'] }}</p></div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Donor</th>
                        <th class="p-3">Recipient</th>
                        <th class="p-3">Score</th>
                        <th class="p-3">Priority</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentMatches as $match)
                        <tr class="hover:bg-slate-50 transition-all duration-200">
                            <td class="p-3">{{ $match->donor->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->recipient->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->score }}</td>
                            <td class="p-3">{{ $match->priority_level }}</td>
                            <td class="p-3">
                                <span class="px-3 py-1 rounded-full text-xs
                                    {{ $match->status === 'APPROVED' ? 'bg-emerald-100 text-emerald-700' : ($match->status === 'REJECTED' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $match->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recent Transplant Activities</h3>
        <div class="space-y-3">
            @forelse ($recentTransplants as $transplant)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-slate-800">
                            {{ $transplant->match->donor->user->name ?? 'Donor' }} -> {{ $transplant->match->recipient->user->name ?? 'Recipient' }}
                        </p>
                        <p class="text-xs text-slate-500">Scheduled: {{ $transplant->scheduled_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-slate-100">{{ $transplant->status }}</span>
                </div>
            @empty
                <p class="text-slate-500 text-sm">No transplant activities yet.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Live Activity Feed</h3>
        <div class="space-y-3">
            @forelse ($activities as $activity)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between">
                    <p class="text-sm text-slate-700">
                        <span class="status-dot status-dot-{{ $activity['type'] ?? 'info' }}"></span>{{ $activity['label'] }}
                    </p>
                    <span class="text-xs text-slate-500">{{ $activity['time']?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No recent activity.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Notification Preview</h3>
        <div class="space-y-2">
            @forelse ($notificationPreview as $notification)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between text-sm">
                    <span>{{ $notification->title }}</span>
                    <span class="text-slate-500">{{ $notification->created_at?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No notifications yet.</p>
            @endforelse
        </div>
        <a href="{{ route('notifications.index') }}" class="inline-block mt-3 text-sm text-[#0b6ea2]">Open full notification center</a>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Audit Logs</h3>
        <div class="space-y-2">
            @forelse ($recentAudits as $log)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-700">{{ strtoupper($log->module) }} - {{ $log->action }}</p>
                        <p class="text-xs text-slate-500">{{ $log->details }}</p>
                    </div>
                    <span class="text-xs text-slate-500">{{ $log->created_at?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No audit events yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
