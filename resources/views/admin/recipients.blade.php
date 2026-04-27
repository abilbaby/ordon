<x-app-layout>
    <x-slot name="title">Recipients</x-slot>

    <div class="card-pro">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="text-lg font-semibold">All Recipients</h3>
            <a href="{{ route('admin.recipients.export.csv', request()->query()) }}" class="rounded-xl bg-[#0b6ea2] text-white px-3 py-2 text-sm">Export CSV</a>
            <form method="GET" class="flex flex-wrap gap-2">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search recipient" class="rounded-xl border-slate-200 text-sm">
                <select name="status" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All status</option>
                    @foreach (['REGISTERED','VERIFIED','MATCHED','REJECTED','COMPLETED'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="urgency" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All urgency</option>
                    @foreach (['high','medium','low'] as $urgency)
                        <option value="{{ $urgency }}" @selected(($filters['urgency'] ?? '') === $urgency)>{{ strtoupper($urgency) }}</option>
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
                    @foreach ($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" @selected((string) ($filters['hospital_id'] ?? '') === (string) $hospital->id)>{{ $hospital->name }}</option>
                    @endforeach
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
                        <th class="p-3">Organ Needed</th>
                        <th class="p-3">Urgency</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Identity</th>
                        <th class="p-3">Doctor Approved</th>
                        <th class="p-3">Emergency Control</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recipients as $recipient)
                        @php
                            $recipientReviewStatus = match (true) {
                                (bool) $recipient->flagged_for_review => 'Flagged Review',
                                (bool) $recipient->admin_approved => 'Approved',
                                $recipient->status === 'REJECTED' => 'Rejected',
                                (bool) $recipient->hospital_verified => 'Hospital Verified',
                                default => 'Pending',
                            };
                            $recipientReviewClass = match ($recipientReviewStatus) {
                                'Approved' => 'bg-emerald-100 text-emerald-700',
                                'Rejected' => 'bg-rose-100 text-rose-700',
                                'Flagged Review' => 'bg-amber-100 text-amber-700',
                                'Hospital Verified' => 'bg-cyan-100 text-cyan-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition-all duration-200" x-data="{ edit: false }">
                            <td class="p-3">{{ $recipient->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $recipient->blood_group }}</td>
                            <td class="p-3">{{ $recipient->organ_needed }}</td>
                            <td class="p-3 uppercase">{{ $recipient->urgency_level }}</td>
                            <td class="p-3">{{ $recipient->status }}</td>
                            <td class="p-3 text-xs">
                                {{ $recipient->identity_type ? strtoupper($recipient->identity_type).' / '.$recipient->masked_identity : 'Not submitted' }}
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $recipient->identity_verified ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $recipient->identity_verified ? 'Verified' : 'Pending' }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-3">{{ $recipient->doctor_approved ? 'Yes' : 'Pending' }}</td>
                            <td class="p-3">
                                <div x-show="!edit" class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] {{ $recipientReviewClass }}">{{ $recipientReviewStatus }}</span>
                                    <button type="button" @click="edit = true" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Edit</button>
                                </div>
                                <div x-show="edit" x-transition class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.recipients.emergency-priority', $recipient) }}">@csrf<button class="rounded-lg bg-rose-600 text-white px-3 py-1.5 text-xs">Set Emergency</button></form>
                                    @if (! $recipient->identity_verified)
                                        <form method="POST" action="{{ route('admin.recipients.verify-identity', $recipient) }}">@csrf<button class="rounded-lg bg-indigo-700 text-white px-3 py-1.5 text-xs">Verify ID</button></form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.recipients.override-approval', $recipient) }}">
                                        @csrf
                                        <input type="hidden" name="approved" value="1">
                                        <button class="rounded-lg bg-emerald-600 text-white px-3 py-1.5 text-xs">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.recipients.override-approval', $recipient) }}">
                                        @csrf
                                        <input type="hidden" name="approved" value="0">
                                        <button class="rounded-lg bg-slate-900 text-white px-3 py-1.5 text-xs">Reject</button>
                                    </form>
                                    <button type="button" @click="edit = false" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Done</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-3">No recipient records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $recipients->links() }}</div>
    </div>
</x-app-layout>
