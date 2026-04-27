<x-app-layout>
    <x-slot name="title">My Request</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Recipient Request Details</h3>
        <p class="text-sm text-slate-600 mb-4">This request profile is maintained by your hospital team after invitation-based onboarding.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-slate-700">
            <div><strong>Blood Group:</strong> {{ $recipient->blood_group }}</div>
            <div><strong>Organ Needed:</strong> {{ $recipient->organ_needed }}</div>
            <div><strong>Urgency Level:</strong> {{ strtoupper($recipient->urgency_level) }}</div>
            <div><strong>Priority Band:</strong>
                @if ($recipient->urgency_level === 'high')
                    Critical
                @elseif($recipient->urgency_level === 'medium')
                    High
                @else
                    Standard
                @endif
            </div>
            <div><strong>Waiting Time:</strong> {{ $recipient->waiting_time }} days</div>
            <div><strong>Status:</strong> {{ $recipient->status }}</div>
        </div>
    </div>
</x-app-layout>
