<x-app-layout>
    <x-slot name="title">Update History</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h1 class="text-xl font-semibold text-gray-900">Update History</h1>
            <p class="text-sm text-gray-600 mt-1">View approved and rejected update requests</p>
        </div>

        <!-- History List -->
        @forelse ($history as $item)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $item->recipient->user->name }}</h3>
                    <p class="text-sm text-gray-600">Submitted: {{ $item->created_at?->format('Y-m-d H:i') }}</p>
                    @if ($item->reviewed_at)
                        <p class="text-xs text-gray-500">Reviewed: {{ $item->reviewed_at?->format('Y-m-d H:i') }}</p>
                    @endif
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $item->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $item->status }}
                </span>
            </div>

            <!-- Changed Fields Summary -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Changed Fields:</h4>
                <div class="flex flex-wrap gap-2">
                    @php
                        $changes = [];
                        if ($item->requested_full_name && $item->requested_full_name !== $item->recipient->user->name) $changes[] = 'Name';
                        if ($item->requested_blood_group && $item->requested_blood_group !== $item->recipient->blood_group) $changes[] = 'Blood Group';
                        if ($item->requested_organ_needed && $item->requested_organ_needed !== $item->recipient->organ_needed) $changes[] = 'Organ';
                        if ($item->requested_urgency_level && $item->requested_urgency_level !== $item->recipient->urgency_level) $changes[] = 'Urgency';
                        if ($item->requested_dob && $item->requested_dob !== $item->recipient->date_of_birth) $changes[] = 'DOB';
                        if ($item->requested_gender && $item->requested_gender !== $item->recipient->gender) $changes[] = 'Gender';
                        if ($item->requested_waiting_time && $item->requested_waiting_time !== $item->recipient->waiting_time) $changes[] = 'Waiting Time';
                        if ($item->requested_other_organs && json_decode($item->requested_other_organs) !== $item->recipient->organs_needed) $changes[] = 'Other Organs';
                    @endphp
                    @foreach ($changes as $change)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $change }}</span>
                    @endforeach
                </div>
            </div>

            @if ($item->reason)
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Reason:</h4>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-md">{{ \Illuminate\Support\Str::limit($item->reason, 150) }}</p>
                </div>
            @endif

            <!-- Action Button -->
            <div class="flex gap-3">
                <a href="{{ route('hospital.update-history.details', $item->id) }}" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800">
                    View Details
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-12">
            <div class="text-center">
                <div class="w-16 h-16 text-gray-400 mx-auto mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No history found</h3>
                <p class="text-sm text-gray-600">There are no approved or rejected update requests in the history.</p>
            </div>
        </div>
        @endforelse
    </div>
</x-app-layout>
