<x-app-layout>
    <x-slot name="title">Edit Profile Request</x-slot>

    <div class="card-pro mb-6">
        <h3 class="text-lg font-semibold mb-2">Request Recipient Profile Correction</h3>
        <p class="text-sm text-slate-600">
            Submit correction requests here. Your hospital team reviews and confirms the change before it updates your recipient details.
        </p>
    </div>

    <div class="card-pro">
        <form method="POST" action="{{ route('recipient.edit-profile.submit') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <div>
                <label class="text-sm text-slate-600">Blood Group</label>
                <select name="blood_group" class="mt-1 form-control @error('blood_group') form-control-invalid @enderror" required>
                    @foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $group)
                        <option value="{{ $group }}" @selected(old('blood_group', $recipient->blood_group) === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                @error('blood_group') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-slate-600">Organ Needed</label>
                <input name="organ_needed" value="{{ old('organ_needed', $recipient->organ_needed) }}" class="mt-1 form-control @error('organ_needed') form-control-invalid @enderror" maxlength="255" required>
                @error('organ_needed') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-slate-600">Urgency</label>
                <select name="urgency_level" class="mt-1 form-control @error('urgency_level') form-control-invalid @enderror" required>
                    @foreach (['high','medium','low'] as $urgency)
                        <option value="{{ $urgency }}" @selected(old('urgency_level', $recipient->urgency_level) === $urgency)>{{ strtoupper($urgency) }}</option>
                    @endforeach
                </select>
                @error('urgency_level') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-slate-600">Waiting Time (days)</label>
                <input type="number" min="0" max="3650" name="waiting_time" value="{{ old('waiting_time', $recipient->waiting_time) }}" class="mt-1 form-control @error('waiting_time') form-control-invalid @enderror" required>
                @error('waiting_time') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-slate-600">Region</label>
                <input name="region" value="{{ old('region', $recipient->region) }}" class="mt-1 form-control @error('region') form-control-invalid @enderror" minlength="2" maxlength="100">
                @error('region') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-slate-600">Other Organs Needed (comma separated)</label>
                <input name="organs_needed" value="{{ old('organs_needed', is_array($recipient->organs_needed ?? null) ? implode(', ', $recipient->organs_needed) : '') }}" class="mt-1 form-control @error('organs_needed') form-control-invalid @enderror" placeholder="Kidney, Liver" maxlength="255">
                @error('organs_needed') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="text-sm text-slate-600">Reason for correction</label>
                <textarea name="reason" rows="3" class="mt-1 form-control @error('reason') form-control-invalid @enderror" maxlength="1000" required>{{ old('reason') }}</textarea>
                @error('reason') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <button class="md:col-span-2 rounded-xl bg-slate-900 text-white px-4 py-2.5 font-semibold disabled:cursor-not-allowed disabled:opacity-60" :disabled="submitting">
                <span x-show="!submitting">Submit to Hospital for Confirmation</span>
                <span x-show="submitting">Submitting...</span>
            </button>
        </form>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recent Correction Requests</h3>
        <div class="space-y-2">
            @forelse ($recentRequests as $item)
                <div class="rounded-xl border border-[#d7e8f4] p-3 text-sm flex items-center justify-between">
                    <div>
                        <p class="font-medium">Requested Organ: {{ $item->payload['organ_needed'] ?? 'N/A' }} / Urgency: {{ strtoupper($item->payload['urgency_level'] ?? 'N/A') }}</p>
                        <p class="text-slate-500">{{ $item->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-[11px] {{ $item->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($item->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ strtoupper($item->status) }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No change requests submitted yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
