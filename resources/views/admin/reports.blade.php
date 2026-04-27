<x-app-layout>
    <x-slot name="title">Reports</x-slot>

    <div class="mb-6 flex gap-3">
        <a href="{{ route('admin.reports.export.csv') }}"
           class="rounded-xl bg-[#0b6ea2] text-white px-4 py-2.5 hover:bg-[#0a5f8b] transition-all duration-200">
            Export CSV
        </a>
        <a href="{{ route('admin.reports.export.pdf') }}"
           class="rounded-xl bg-slate-900 text-white px-4 py-2.5 hover:bg-slate-700 transition-all duration-200">
            Export PDF
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="card-pro">
            <p class="text-slate-500">Total Transplants</p>
            <p class="text-3xl font-semibold mt-2">{{ $totalTransplants }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Completed</p>
            <p class="text-3xl font-semibold mt-2">{{ $completedTransplants }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Approval Rate</p>
            <p class="text-3xl font-semibold mt-2">{{ $approvalRate }}%</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Open Issue Reports</p>
            <p class="text-3xl font-semibold mt-2">{{ $openIssueReports }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Resolved Reports</p>
            <p class="text-3xl font-semibold mt-2">{{ $resolvedIssueReports }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Success Rate</p>
            <p class="text-3xl font-semibold mt-2">{{ $successRate }}%</p>
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recipient Queue Analytics by Organ Type</h3>
        <div class="space-y-3">
            @forelse ($queueByOrgan as $row)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-slate-700 capitalize">{{ $row->organ_needed }}</span>
                        <span class="text-slate-500">{{ $row->total }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-[#0b6ea2]" style="width: {{ min($row->total * 12, 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No recipient queue data available.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recipient Queue by Blood Group</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @forelse ($queueByBlood as $row)
                <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3 text-center">
                    <p class="text-xs text-slate-500">{{ $row->blood_group }}</p>
                    <p class="text-xl font-semibold text-[#0b3650] mt-1">{{ $row->total }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500 col-span-full">No blood-group queue data.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Operational Intelligence Highlights</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-[#d7e8f4] p-4 bg-slate-50">
                <p class="font-medium text-slate-800">Approval Bottleneck Watch</p>
                <p class="text-sm text-slate-500 mt-1">Tracks where cases slow down between verification, matching, and clinical approval.</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] p-4 bg-slate-50">
                <p class="font-medium text-slate-800">Risk and Compliance Pulse</p>
                <p class="text-sm text-slate-500 mt-1">Summarizes fraud flags, blacklisted entities, and unresolved issue reports for fast review.</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] p-4 bg-slate-50">
                <p class="font-medium text-slate-800">Outcome Quality Snapshot</p>
                <p class="text-sm text-slate-500 mt-1">Highlights completion trend, region-wise donation contribution, and recent transplant outcomes.</p>
            </div>
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Donations Per Region</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @forelse ($donationsByRegion as $row)
                <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3 text-center">
                    <p class="text-xs text-slate-500">{{ $row->region }}</p>
                    <p class="text-xl font-semibold text-[#0b3650] mt-1">{{ $row->total }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500 col-span-full">No region data available.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">System Audit Logs</h3>
        <div class="space-y-2">
            @forelse ($auditLogs as $log)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-700">{{ strtoupper($log->module) }} - {{ $log->action }}</p>
                        <p class="text-xs text-slate-500">{{ $log->details }}</p>
                    </div>
                    <span class="text-xs text-slate-500">{{ $log->created_at?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No audit logs yet.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Overall Role Reports (Donor/Recipient/Hospital)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">User</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Scope</th>
                        <th class="p-3">Subject</th>
                        <th class="p-3">Message</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($issueReports as $report)
                        <tr class="hover:bg-slate-50 transition-all duration-200">
                            <td class="p-3">{{ $report->user->name ?? 'User' }}</td>
                            <td class="p-3 capitalize">{{ $report->role }}</td>
                            <td class="p-3 uppercase text-xs">{{ $report->scope }}</td>
                            <td class="p-3">{{ $report->subject }}</td>
                            <td class="p-3 text-sm text-slate-700">{{ $report->message }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-[11px] {{ $report->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ strtoupper($report->status) }}
                                </span>
                            </td>
                            <td class="p-3">
                                @if ($report->status !== 'resolved')
                                    <form method="POST" action="{{ route('admin.issue-reports.resolve', $report) }}">
                                        @csrf
                                        <button class="rounded-lg bg-emerald-700 text-white px-3 py-1.5 text-xs">Mark Resolved</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.issue-reports.reopen', $report) }}">
                                        @csrf
                                        <button class="rounded-lg bg-slate-700 text-white px-3 py-1.5 text-xs">Reopen</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-3" colspan="7">No role reports submitted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
