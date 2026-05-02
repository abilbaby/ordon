<x-app-layout>
    <x-slot name="title">Matches</x-slot>

    <div class="card-pro">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="text-lg font-semibold">All Matches</h3>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.auto-matching') }}" class="inline">
                    @csrf
                    <button type="submit" class="rounded-xl bg-green-600 hover:bg-green-700 text-white px-3 py-2 text-sm transition-colors">Run Auto Matching</button>
                </form>
                <a href="{{ route('admin.matches.export.csv', request()->query()) }}" class="rounded-xl bg-[#0b6ea2] text-white px-3 py-2 text-sm">Export CSV</a>
            </div>
            <form method="GET" class="flex flex-wrap gap-2">
                <select name="status" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All status</option>
                    @foreach (['MATCHED','APPROVED','REJECTED','COMPLETED'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="priority" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All priority</option>
                    @foreach (['Critical','High','Medium','Standard'] as $priority)
                        <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ $priority }}</option>
                    @endforeach
                </select>
                <select name="organ_type" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All organs</option>
                    @foreach (['Kidney','Liver','Heart','Lung','Pancreas','Intestine'] as $organ)
                        <option value="{{ $organ }}" @selected(($filters['organ_type'] ?? '') === $organ)>{{ $organ }}</option>
                    @endforeach
                </select>
                <select name="urgency" class="rounded-xl border-slate-200 text-sm">
                    <option value="">All urgency</option>
                    @foreach (['high','medium','low'] as $urgency)
                        <option value="{{ $urgency }}" @selected(($filters['urgency'] ?? '') === $urgency)>{{ ucfirst($urgency) }}</option>
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
                        <th class="p-3">Donor</th>
                        <th class="p-3">Recipient</th>
                        <th class="p-3">Score</th>
                        <th class="p-3">Priority</th>
                        <th class="p-3">Breakdown</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Override</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($matches as $match)
                        <tr class="hover:bg-slate-50 transition-all duration-200">
                            <td class="p-3">{{ $match->donor->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->recipient->user->name ?? 'N/A' }}</td>
                            <td class="p-3">
                                <div class="font-semibold">{{ $match->match_score ?? $match->score }}%</div>
                                <div class="text-xs text-slate-500">Match Score</div>
                            </td>
                            <td class="p-3">{{ $match->priority_level }}</td>
                            <td class="p-3 text-xs text-slate-600">
                                <div>{{ $match->reason ?? 'Reason not available' }}</div>
                                <div class="mt-1">
                                    <div>Breakdown:</div>
                                    <div>{{ ($match->score_breakdown['compatibility'] ?? 0) > 0 ? '✔' : '✖' }} Blood Match</div>
                                    <div>{{ ($match->score_breakdown['urgency'] ?? 0) > 0 ? '✔' : '✖' }} Urgency Level</div>
                                    <div>{{ ($match->score_breakdown['distance'] ?? 0) > 0 ? '✔' : '✖' }} Distance Level</div>
                                </div>
                            </td>
                            <td class="p-3">{{ $match->status }}</td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('admin.matches.override', $match) }}" class="space-y-2">
                                    @csrf
                                    <select name="status" class="rounded-lg border-slate-200 text-xs">
                                        @foreach (['MATCHED','APPROVED','REJECTED','COMPLETED'] as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <input name="override_reason" class="rounded-lg border-slate-200 text-xs w-40" placeholder="Reason" required>
                                    <button class="rounded-lg bg-slate-900 text-white px-2 py-1 text-xs">Apply</button>
                                </form>
                                @if ($match->transplant)
                                    <form method="POST" action="{{ route('admin.transplants.certificate-recipient', $match->transplant) }}" class="space-y-2 mt-2">
                                        @csrf
                                        <input name="recipient_name_override" value="{{ $match->transplant->recipient_name_override }}" class="rounded-lg border-slate-200 text-xs w-40" placeholder="Certificate recipient name" minlength="2" maxlength="100" pattern="[A-Za-z ]+">
                                        <button class="rounded-lg bg-amber-700 text-white px-2 py-1 text-xs">Set Cert Name</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-3">No match records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $matches->links() }}</div>
    </div>
</x-app-layout>
