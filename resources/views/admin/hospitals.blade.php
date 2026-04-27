<x-app-layout>
    <x-slot name="title">Hospitals</x-slot>

    <div class="card-pro">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="text-lg font-semibold">Registered Hospitals</h3>
            <a href="{{ route('admin.hospitals.export.csv', request()->query()) }}" class="rounded-xl bg-[#0b6ea2] text-white px-3 py-2 text-sm">Export CSV</a>
            <form method="GET" class="flex flex-wrap gap-2">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search hospital" class="rounded-xl border-slate-200 text-sm">
                <select name="approved" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All</option>
                    <option value="yes" @selected(($filters['approved'] ?? '') === 'yes')>Approved</option>
                    <option value="no" @selected(($filters['approved'] ?? '') === 'no')>Not approved</option>
                </select>
                <select name="fraud" class="rounded-xl border-slate-200 text-sm">
                    <option value="">Fraud: All</option>
                    <option value="yes" @selected(($filters['fraud'] ?? '') === 'yes')>Fraud Flagged</option>
                    <option value="no" @selected(($filters['fraud'] ?? '') === 'no')>Not Flagged</option>
                </select>
                <select name="blacklisted" class="rounded-xl border-slate-200 text-sm">
                    <option value="">Blacklist: All</option>
                    <option value="yes" @selected(($filters['blacklisted'] ?? '') === 'yes')>Blacklisted</option>
                    <option value="no" @selected(($filters['blacklisted'] ?? '') === 'no')>Not Blacklisted</option>
                </select>
                <button class="rounded-xl bg-slate-900 text-white px-3 py-2 text-sm">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Hospital</th>
                        <th class="p-3">Location</th>
                        <th class="p-3">Approved</th>
                        <th class="p-3">Identity</th>
                        <th class="p-3">Risk</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hospitals as $hospital)
                        @php
                            $reviewStatus = match (true) {
                                (bool) $hospital->blacklisted => 'Blacklisted',
                                (bool) $hospital->fraud_flag => 'Fraud Flagged',
                                (bool) $hospital->approved => 'Approved',
                                default => 'Rejected/Pending',
                            };
                            $reviewStatusClass = match ($reviewStatus) {
                                'Approved' => 'bg-emerald-100 text-emerald-700',
                                'Fraud Flagged' => 'bg-amber-100 text-amber-700',
                                'Blacklisted' => 'bg-slate-900 text-white',
                                default => 'bg-rose-100 text-rose-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition-all duration-200" x-data="{ edit: false }">
                            <td class="p-3">{{ $hospital->name }}</td>
                            <td class="p-3">{{ $hospital->location }}</td>
                            <td class="p-3">{{ $hospital->approved ? 'Yes' : 'No' }}</td>
                            <td class="p-3 text-xs">
                                @php $masked = $hospital->identity_number ? str_repeat('*', max(strlen($hospital->identity_number) - 4, 0)).substr($hospital->identity_number, -4) : 'N/A'; @endphp
                                {{ $hospital->identity_type ? strtoupper($hospital->identity_type).' / '.$masked : 'Not submitted' }}
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $hospital->identity_verified ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $hospital->identity_verified ? 'Verified' : 'Pending' }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-3 text-xs">
                                {{ $hospital->fraud_flag ? 'Fraud Flagged' : 'Clear' }} /
                                {{ $hospital->blacklisted ? 'Blacklisted' : 'Active' }}
                            </td>
                            <td class="p-3">
                                <div x-show="!edit" class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] {{ $reviewStatusClass }}">{{ $reviewStatus }}</span>
                                    <button type="button" @click="edit = true" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Edit</button>
                                </div>
                                <div x-show="edit" x-transition class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.hospitals.approve', $hospital) }}">@csrf<button class="rounded-lg bg-emerald-600 text-white px-2 py-1 text-xs">Approve</button></form>
                                    <form method="POST" action="{{ route('admin.hospitals.reject', $hospital) }}">@csrf<button class="rounded-lg bg-rose-600 text-white px-2 py-1 text-xs">Reject</button></form>
                                    <form method="POST" action="{{ route('admin.hospitals.flag-fraud', $hospital) }}">@csrf<button class="rounded-lg bg-amber-600 text-white px-2 py-1 text-xs">Flag Fraud</button></form>
                                    <form method="POST" action="{{ route('admin.hospitals.blacklist', $hospital) }}">@csrf<button class="rounded-lg bg-slate-900 text-white px-2 py-1 text-xs">Blacklist</button></form>
                                    @if (! $hospital->identity_verified)
                                        <form method="POST" action="{{ route('admin.hospitals.verify-identity', $hospital) }}">@csrf<button class="rounded-lg bg-indigo-700 text-white px-2 py-1 text-xs">Verify ID</button></form>
                                    @endif
                                    <button type="button" @click="edit = false" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Done</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-3">No hospital records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $hospitals->links() }}</div>
    </div>
</x-app-layout>
