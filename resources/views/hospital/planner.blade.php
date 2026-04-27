<x-app-layout>
    <x-slot name="title">Hospital Slot Planner</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Calendar / Slot Planning (Lightweight)</h3>
        <p class="text-sm text-slate-600 mb-6">
            Assign operation slots to approved transplants by selecting date, period, operating room, and surgeon.
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Donor</th>
                        <th class="p-3">Recipient</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Current Slot</th>
                        <th class="p-3">Assign / Update Slot</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($upcoming as $transplant)
                        <tr class="hover:bg-slate-50 transition-all duration-200 align-top">
                            <td class="p-3">{{ $transplant->match->donor->user->name ?? 'Donor' }}</td>
                            <td class="p-3">{{ $transplant->match->recipient->user->name ?? 'Recipient' }}</td>
                            <td class="p-3">{{ $transplant->status }}</td>
                            <td class="p-3 text-xs text-slate-600">
                                {{ $transplant->slot_date?->format('Y-m-d') ?? 'Not assigned' }}
                                @if ($transplant->slot_period)
                                    ({{ $transplant->slot_period }})<br>
                                    Room: {{ $transplant->operating_room }}<br>
                                    Surgeon: {{ $transplant->surgeon_name }}
                                @endif
                            </td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('hospital.transplants.slot', $transplant) }}" class="grid grid-cols-2 gap-2">
                                    @csrf
                                    <input type="date" name="slot_date" class="rounded-xl border-[#c8dfef]" required>
                                    <select name="slot_period" class="rounded-xl border-[#c8dfef]" required>
                                        <option value="Morning">Morning</option>
                                        <option value="Afternoon">Afternoon</option>
                                        <option value="Evening">Evening</option>
                                    </select>
                                    <input name="operating_room" class="rounded-xl border-[#c8dfef] col-span-2" placeholder="Operating Room (e.g. OR-2)" required>
                                    <input name="surgeon_name" class="rounded-xl border-[#c8dfef] col-span-2" placeholder="Lead Surgeon Name" required>
                                    <button class="rounded-xl bg-[#0b6ea2] text-white px-3 py-2 text-sm col-span-2 hover:bg-[#0a5f8b]">Save Slot</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3">No approved transplants available for planning.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $upcoming->links() }}</div>
    </div>
</x-app-layout>
