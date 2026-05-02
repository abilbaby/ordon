<x-app-layout>
    <x-slot name="title">Hospital Slot Planner</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Calendar / Slot Planning</h3>
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
                        <tr class="hover:bg-slate-50 transition-all duration-200 align-top border-b">
                            <td class="p-3">{{ $transplant->match->donor->user->name ?? 'Donor' }}</td>
                            <td class="p-3">{{ $transplant->match->recipient->user->name ?? 'Recipient' }}</td>
                            <td class="p-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $transplant->status === 'COMPLETED' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $transplant->status }}
                                </span>
                            </td>
                            <td class="p-3 text-xs text-slate-600">
                                @if ($transplant->slot_date)
                                    <p><strong>{{ $transplant->slot_date->format('Y-m-d') }}</strong> ({{ $transplant->slot_period }})</p>
                                    <p>Room: {{ $transplant->operating_room }}</p>
                                    <p>Surgeon: {{ $transplant->doctor?->name ?? $transplant->surgeon_name }}</p>
                                @else
                                    <p class="text-slate-400">Not assigned</p>
                                @endif
                            </td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('hospital.transplants.slot', $transplant) }}" class="grid grid-cols-2 gap-2">
                                    @csrf
                                    @method('POST')
                                    
                                    <input type="date" name="slot_date" class="rounded-lg border border-slate-300 p-2 text-sm col-span-2" required>
                                    
                                    <select name="slot_period" class="rounded-lg border border-slate-300 p-2 text-sm" required>
                                        <option value="">Select Period</option>
                                        <option value="Morning">Morning</option>
                                        <option value="Afternoon">Afternoon</option>
                                        <option value="Evening">Evening</option>
                                    </select>
                                    
                                    <input type="text" name="operating_room" class="rounded-lg border border-slate-300 p-2 text-sm" placeholder="Room (e.g. OR-2)" maxlength="100" required>
                                    
                                    <select name="doctor_id" class="rounded-lg border border-slate-300 p-2 text-sm col-span-2" required>
                                        <option value="">Select Surgeon</option>
                                        @foreach ($hospital->doctors as $doctor)
                                            <option value="{{ $doctor->id }}">
                                                {{ $doctor->name }} ({{ $doctor->specialization }})
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <button type="submit" class="rounded-lg bg-blue-600 text-white px-3 py-2 text-sm col-span-2 hover:bg-blue-700 font-medium">
                                        Save Slot
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-3 text-center text-slate-500">No approved transplants available for planning.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $upcoming->links() }}</div>
    </div>
</x-app-layout>
