<x-app-layout>
    <x-slot name="title">My Request</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Recipient Request Details</h3>
        <p class="text-sm text-slate-600 mb-4">This request profile is maintained by your hospital team after invitation-based onboarding.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-slate-700">
            <div class="rounded-xl bg-slate-50 p-3"><span class="text-sm text-slate-500">Blood Group</span><p class="font-semibold">{{ $recipient->blood_group }}</p></div>
            <div class="rounded-xl bg-slate-50 p-3"><span class="text-sm text-slate-500">Organ Needed</span><p class="font-semibold">{{ $recipient->organ_needed }}</p></div>
            <div class="rounded-xl bg-slate-50 p-3"><span class="text-sm text-slate-500">Urgency Level</span><p class="font-semibold">{{ strtoupper($recipient->urgency_level) }}</p></div>
            <div class="rounded-xl bg-slate-50 p-3"><span class="text-sm text-slate-500">Priority Band</span><p class="font-semibold">
                @if ($recipient->urgency_level === 'high')
                    Critical
                @elseif($recipient->urgency_level === 'medium')
                    High
                @else
                    Standard
                @endif</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-3"><span class="text-sm text-slate-500">Waiting Time</span><p class="font-semibold">{{ $recipient->waiting_time }} days</p></div>
            <div class="rounded-xl bg-slate-50 p-3"><span class="text-sm text-slate-500">Status</span><p class="font-semibold">{{ $recipient->status }}</p></div>
        </div>
    </div>
</x-app-layout>
