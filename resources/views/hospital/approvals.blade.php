<x-app-layout>
    <x-slot name="title">Approvals</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Pending Match Approvals</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Donor</th>
                        <th class="p-3">Recipient</th>
                        <th class="p-3">Score</th>
                        <th class="p-3">Priority</th>
                        <th class="p-3">Explanation</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($matches as $match)
                        <tr class="hover:bg-slate-50 transition-all duration-200">
                            <td class="p-3">{{ $match->donor->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->recipient->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->match_score ?? $match->score }}%</td>
                            <td class="p-3">{{ $match->priority_level }}</td>
                            <td class="p-3 text-xs text-slate-600">
                                <div>{{ $match->reason ?? 'No explanation available' }}</div>
                                <div class="mt-1">{{ ($match->score_breakdown['compatibility'] ?? 0) > 0 ? '✔' : '✖' }} Blood</div>
                                <div>{{ ($match->score_breakdown['urgency'] ?? 0) > 0 ? '✔' : '✖' }} Urgency</div>
                                <div>{{ ($match->score_breakdown['distance'] ?? 0) > 0 ? '✔' : '✖' }} Distance</div>
                            </td>
                            <td class="p-3 flex gap-2">
                                <form method="POST" action="{{ route('hospital.matches.approve', $match) }}">
                                    @csrf
                                    <button class="rounded-xl bg-emerald-600 text-white px-4 py-2">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('hospital.matches.reject', $match) }}">
                                    @csrf
                                    <button class="rounded-xl bg-rose-600 text-white px-4 py-2">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-3">No pending approvals.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $matches->links() }}</div>
    </div>
</x-app-layout>
