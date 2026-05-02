<x-app-layout>
    <x-slot name="title">Donors</x-slot>

    <div class="card-pro">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="text-lg font-semibold">All Donors</h3>
            <a href="{{ route('admin.donors.export.csv', request()->query()) }}" class="rounded-xl bg-[#0b6ea2] text-white px-3 py-2 text-sm">Export CSV</a>
            <form method="GET" class="flex flex-wrap gap-2">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search donor" class="rounded-xl border-slate-200 text-sm">
                <select name="status" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All statuses</option>
                    @foreach (['REGISTERED','VERIFIED','MATCHED','REJECTED','COMPLETED'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="organ_type" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All organs</option>
                    @foreach (['Kidney','Liver','Heart','Lung','Pancreas','Intestine'] as $organ)
                        <option value="{{ $organ }}" @selected(($filters['organ_type'] ?? '') === $organ)>{{ $organ }}</option>
                    @endforeach
                </select>
                <select name="blood_group" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All blood groups</option>
                    @foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $group)
                        <option value="{{ $group }}" @selected(($filters['blood_group'] ?? '') === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                <select name="hospital_id" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All hospitals</option>
                    @foreach ($hospitals ?? [] as $hospital)
                        <option value="{{ $hospital->id }}" @selected(($filters['hospital_id'] ?? '') === (string) $hospital->id)>{{ $hospital->name }}</option>
                    @endforeach
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
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rounded-xl border-slate-200 text-sm">
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="rounded-xl border-slate-200 text-sm">
                <button class="rounded-xl bg-slate-900 text-white px-3 py-2 text-sm">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Name</th>
                        <th class="p-3">Blood Group</th>
                        <th class="p-3">Organ</th>
                        <th class="p-3">Region</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Identity</th>
                        <th class="p-3">Risk</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donors as $donor)
                        @php
                            $reviewStatus = match (true) {
                                (bool) $donor->blacklisted => 'Blacklisted',
                                (bool) $donor->fraud_flag => 'Fraud Flagged',
                                (bool) $donor->approved && $donor->medical_status === 'VERIFIED' => 'Approved',
                                $donor->medical_status === 'REJECTED' => 'Rejected',
                                default => 'Pending',
                            };
                            $reviewStatusClass = match ($reviewStatus) {
                                'Approved' => 'bg-emerald-100 text-emerald-700',
                                'Rejected' => 'bg-rose-100 text-rose-700',
                                'Fraud Flagged' => 'bg-amber-100 text-amber-700',
                                'Blacklisted' => 'bg-slate-900 text-white',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition-all duration-200" x-data="{ edit: false }">
                            <td class="p-3">{{ $donor->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $donor->blood_group }}</td>
                            <td class="p-3">{{ $donor->organ_type }}</td>
                            <td class="p-3">{{ $donor->region ?? 'N/A' }}</td>
                            <td class="p-3">{{ $donor->medical_status }}</td>
                            <td class="p-3 text-xs">
                                @php $masked = $donor->identity_number ? str_repeat('*', max(strlen($donor->identity_number) - 4, 0)).substr($donor->identity_number, -4) : 'N/A'; @endphp
                                {{ $donor->identity_type ? strtoupper($donor->identity_type).' / '.$masked : 'Not submitted' }}
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $donor->identity_verified ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $donor->identity_verified ? 'Verified' : 'Pending' }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-3 text-xs">
                                {{ $donor->fraud_flag ? 'Fraud Flagged' : 'Clear' }} /
                                {{ $donor->blacklisted ? 'Blacklisted' : 'Active' }}
                            </td>
                            <td class="p-3">
                                <div x-show="!edit" class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] {{ $reviewStatusClass }}">{{ $reviewStatus }}</span>
                                    <button type="button" @click="edit = true" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Edit</button>
                                </div>
                                <div x-show="edit" x-transition class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.donors.approve', $donor) }}">@csrf<button class="rounded-lg bg-emerald-600 text-white px-2 py-1 text-xs">Approve</button></form>
                                    <form method="POST" action="{{ route('admin.donors.reject', $donor) }}">@csrf<button class="rounded-lg bg-rose-600 text-white px-2 py-1 text-xs">Reject</button></form>
                                    <form method="POST" action="{{ route('admin.donors.flag-fraud', $donor) }}">@csrf<button class="rounded-lg bg-amber-600 text-white px-2 py-1 text-xs">Flag Fraud</button></form>
                                    <form method="POST" action="{{ route('admin.donors.blacklist', $donor) }}">@csrf<button class="rounded-lg bg-slate-900 text-white px-2 py-1 text-xs">Blacklist</button></form>
                                    @if (! $donor->identity_verified)
                                        <form method="POST" action="{{ route('admin.donors.verify-identity', $donor) }}">@csrf<button class="rounded-lg bg-indigo-700 text-white px-2 py-1 text-xs">Verify ID</button></form>
                                    @endif
                                    <button type="button" @click="edit = false" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Done</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-3">No donor records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $donors->links() }}</div>
    </div>
</x-app-layout>
