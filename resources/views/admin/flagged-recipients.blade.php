<x-app-layout>
    <x-slot name="title">Flagged Recipients</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Recipients Pending Manual Review</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Hospital</th>
                        <th class="p-3">Blood Group</th>
                        <th class="p-3">Government ID</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recipients as $recipient)
                        <tr class="border-b border-slate-100">
                            <td class="p-3">{{ $recipient->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $recipient->user->email ?? 'N/A' }}</td>
                            <td class="p-3">{{ $recipient->hospital->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $recipient->blood_group }}</td>
                            <td class="p-3">{{ strtoupper($recipient->identity_type ?? 'N/A') }} - {{ $recipient->masked_identity }}</td>
                            <td class="p-3 flex gap-2">
                                <form method="POST" action="{{ route('admin.recipients.override-approval', $recipient) }}">
                                    @csrf
                                    <input type="hidden" name="approved" value="1">
                                    <button class="rounded-lg bg-emerald-600 text-white px-3 py-1.5 text-xs">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.recipients.override-approval', $recipient) }}">
                                    @csrf
                                    <input type="hidden" name="approved" value="0">
                                    <button class="rounded-lg bg-rose-600 text-white px-3 py-1.5 text-xs">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-3 text-slate-500">No flagged recipients.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
