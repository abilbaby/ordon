<x-app-layout>
    <x-slot name="title">My Matches</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Matched Recipients</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Recipient</th>
                        <th class="p-3">Score</th>
                        <th class="p-3">Priority</th>
                        <th class="p-3">Why This Match</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($matches as $match)
                        <tr class="hover:bg-slate-50 transition-all duration-200">
                            <td class="p-3">{{ $match->recipient->user->name ?? 'N/A' }}</td>
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
                        <tr><td colspan="5" class="p-3">No matches available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $matches->links() }}</div>
    </div>
</x-app-layout>
