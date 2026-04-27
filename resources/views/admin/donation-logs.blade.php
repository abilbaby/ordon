<x-app-layout>
    <x-slot name="title">Donation Logs</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">All Donation History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-200">
                        <th class="p-3">Date</th>
                        <th class="p-3">Donor</th>
                        <th class="p-3">Recipient</th>
                        <th class="p-3">Hospital</th>
                        <th class="p-3">Organ</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donationLogs as $log)
                        <tr class="border-b border-slate-100">
                            <td class="p-3">{{ $log->donation_date?->format('Y-m-d') }}</td>
                            <td class="p-3">{{ $log->donor->user->name ?? 'Donor' }}</td>
                            <td class="p-3">{{ $log->recipient->user->name ?? 'Recipient' }}</td>
                            <td class="p-3">{{ $log->hospital->name ?? 'Hospital' }}</td>
                            <td class="p-3">{{ $log->organ_type }}</td>
                            <td class="p-3">{{ $log->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-3 text-slate-500" colspan="6">No donation history records found yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
