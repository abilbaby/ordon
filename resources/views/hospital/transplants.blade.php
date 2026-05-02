<x-app-layout>
    <x-slot name="title">Transplants</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Scheduled & Completed Transplants</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Donor</th>
                        <th class="p-3">Recipient</th>
                        <th class="p-3">Scheduled At</th>
                        <th class="p-3">Slot</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Workflow</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transplants as $transplant)
                        <tr class="hover:bg-slate-50 transition-all duration-200">
                            <td class="p-3">{{ $transplant->match->donor->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $transplant->match->recipient->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $transplant->scheduled_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                            <td class="p-3 text-xs text-slate-600">
                                {{ $transplant->slot_date?->format('Y-m-d') ?? 'Not assigned' }}
                                @if ($transplant->slot_period)
                                    <br>{{ $transplant->slot_period }} / {{ $transplant->operating_room }}
                                @endif
                            </td>
                            <td class="p-3">{{ $transplant->status }}</td>
                            <td class="p-3 text-xs">
                                Surgery: {{ $transplant->surgery_status }}<br>
                                Transport: {{ $transplant->transport_status }}
                            </td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('hospital.transplants.surgery-workflow', $transplant) }}" class="mb-2">
                                    @csrf
                                    <select name="surgery_status" class="rounded-lg border-slate-200 text-xs">
                                        @foreach (['Scheduled','In Progress','Completed'] as $step)
                                            <option value="{{ $step }}" @selected($transplant->surgery_status === $step)>{{ $step }}</option>
                                        @endforeach
                                    </select>
                                    <button class="rounded-lg bg-cyan-700 text-white px-2 py-1 text-xs">Update</button>
                                </form>
                                <form method="POST" action="{{ route('hospital.transplants.transport', $transplant) }}" class="mb-2">
                                    @csrf
                                    <select name="transport_status" class="rounded-lg border-slate-200 text-xs">
                                        @foreach (['Pending','In Transit','Delivered'] as $transport)
                                            <option value="{{ $transport }}" @selected($transplant->transport_status === $transport)>{{ $transport }}</option>
                                        @endforeach
                                    </select>
                                    <button class="rounded-lg bg-indigo-700 text-white px-2 py-1 text-xs">Track</button>
                                </form>
                                <form method="POST" action="{{ route('hospital.transplants.post-op-report', $transplant) }}" class="mb-2">
                                    @csrf
                                    <input name="post_operation_report" class="rounded-lg border-slate-200 text-xs w-40" placeholder="Post-op report">
                                    <button class="rounded-lg bg-slate-700 text-white px-2 py-1 text-xs">Save</button>
                                </form>
                                <form method="POST" action="{{ route('hospital.transplants.certificate-recipient', $transplant) }}" class="mb-2">
                                    @csrf
                                    <input name="recipient_name_override" value="{{ $transplant->recipient_name_override }}" class="rounded-lg border-slate-200 text-xs w-40" placeholder="Certificate recipient name" minlength="2" maxlength="100" pattern="[A-Za-z ]+">
                                    <button class="rounded-lg bg-amber-700 text-white px-2 py-1 text-xs">Set Cert Name</button>
                                </form>
                                @if ($transplant->status !== 'COMPLETED')
                                    <form method="POST" action="{{ route('hospital.matches.complete', $transplant->match_id) }}">
                                        @csrf
                                        <button class="rounded-xl bg-slate-900 text-white px-4 py-2">Mark Complete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-3">No transplants found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $transplants->links() }}</div>
    </div>
</x-app-layout>
