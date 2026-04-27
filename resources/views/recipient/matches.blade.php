<x-app-layout>
    <x-slot name="title">Match Status</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Matches for Your Request</h3>
        <p class="text-sm text-slate-600 mb-4">Track your match status and history here. Any request parameter updates are handled by your hospital.</p>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Donor</th>
                        <th class="p-3">Score</th>
                        <th class="p-3">Priority</th>
                        <th class="p-3">Why You Were Prioritized</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($matches as $match)
                        <tr class="hover:bg-slate-50 transition-all duration-200">
                            <td class="p-3">{{ $match->donor->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->match_score ?? $match->score }}%</td>
                            <td class="p-3">{{ $match->priority_level }}</td>
                            <td class="p-3 text-xs text-slate-600">
                                <div>{{ $match->reason ?? 'Match reason not available.' }}</div>
                                <div class="mt-1">{{ ($match->score_breakdown['compatibility'] ?? 0) > 0 ? '✔' : '✖' }} Blood Match</div>
                                <div>{{ ($match->score_breakdown['urgency'] ?? 0) > 0 ? '✔' : '✖' }} Urgency</div>
                                <div>{{ ($match->score_breakdown['distance'] ?? 0) > 0 ? '✔' : '✖' }} Distance</div>
                            </td>
                            <td class="p-3">{{ $match->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3">No matches found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $matches->links() }}</div>
    </div>
</x-app-layout>
