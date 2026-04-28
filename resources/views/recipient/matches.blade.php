<x-app-layout>
    <x-slot name="title">Match Status</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Matches for Your Request</h3>
        <p class="text-sm text-slate-600 mb-4">Track your match status and history here. Any request parameter updates are handled by your hospital.</p>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="p-3">Donor</th>
                        <th class="p-3">Score</th>
                        <th class="p-3">Priority</th>
                        <th class="p-3">Why You Were Prioritized</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($matches as $match)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3">{{ $match->donor->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->match_score ?? $match->score }}%</td>
                            <td class="p-3">{{ $match->priority_level }}</td>
                            <td class="p-3 text-xs text-slate-600">
                                <div>{{ $match->reason ?? 'Match reason not available.' }}</div>
                                <div class="mt-1">{{ ($match->score_breakdown['compatibility'] ?? 0) > 0 ? 'Yes' : 'No' }} - Blood Match</div>
                                <div>{{ ($match->score_breakdown['urgency'] ?? 0) > 0 ? 'Yes' : 'No' }} - Urgency</div>
                                <div>{{ ($match->score_breakdown['distance'] ?? 0) > 0 ? 'Yes' : 'No' }} - Distance</div>
                            </td>
                            <td class="p-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $match->status === 'APPROVED' ? 'bg-emerald-100 text-emerald-700' : ($match->status === 'REJECTED' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $match->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-slate-500">No matches yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $matches->links() }}</div>
    </div>
</x-app-layout>
