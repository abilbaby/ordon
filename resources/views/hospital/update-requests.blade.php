<x-app-layout>
    <x-slot name="title">Update Requests</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h1 class="text-xl font-semibold text-gray-900">Update Requests</h1>
            <p class="text-sm text-gray-600 mt-1">Review and process recipient update requests</p>
        </div>

        <!-- Pending Requests List -->
        @forelse ($requests as $request)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $request->recipient->user->name }}</h3>
                    <p class="text-sm text-gray-600">Organ: {{ $request->requested_organ_needed }} / Urgency: {{ strtoupper($request->requested_urgency_level) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Submitted: {{ $request->created_at?->format('Y-m-d H:i') }}</p>
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                    Pending
                </span>
            </div>

            <!-- Changed Fields Summary -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Requested Changes:</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @if ($request->requested_full_name && $request->requested_full_name !== $request->recipient->user->name)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Full Name</p>
                                <p class="text-xs text-gray-500">{{ $request->recipient->user->name }} → {{ $request->requested_full_name }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($request->requested_dob && $request->requested_dob !== $request->recipient->date_of_birth)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Date of Birth</p>
                                <p class="text-xs text-gray-500">{{ $request->recipient->date_of_birth?->format('Y-m-d') }} → {{ $request->requested_dob }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($request->requested_gender && $request->requested_gender !== $request->recipient->gender)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Gender</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($request->recipient->gender ?? '') }} → {{ ucfirst($request->requested_gender) }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($request->requested_blood_group && $request->requested_blood_group !== $request->recipient->blood_group)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Blood Group</p>
                                <p class="text-xs text-gray-500">{{ $request->recipient->blood_group }} → {{ $request->requested_blood_group }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($request->requested_organ_needed && $request->requested_organ_needed !== $request->recipient->organ_needed)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Organ Needed</p>
                                <p class="text-xs text-gray-500">{{ $request->recipient->organ_needed }} → {{ $request->requested_organ_needed }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($request->requested_urgency_level && $request->requested_urgency_level !== $request->recipient->urgency_level)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Urgency Level</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($request->recipient->urgency_level ?? '') }} → {{ ucfirst($request->requested_urgency_level) }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($request->requested_waiting_time && $request->requested_waiting_time !== $request->recipient->waiting_time)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Waiting Time</p>
                                <p class="text-xs text-gray-500">{{ $request->recipient->waiting_time }} → {{ $request->requested_waiting_time }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($request->requested_other_organs && json_decode($request->requested_other_organs) !== $request->recipient->organs_needed)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Other Organs</p>
                                <p class="text-xs text-gray-500">{{ is_array($request->recipient->organs_needed) ? implode(', ', $request->recipient->organs_needed) : 'None' }} → {{ is_array(json_decode($request->requested_other_organs)) ? implode(', ', json_decode($request->requested_other_organs)) : $request->requested_other_organs }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($request->reason)
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Reason for Change:</h4>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-md">{{ $request->reason }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <a href="{{ route('update-request.show', $request->id) }}" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800">
                    Review Details
                </a>
                <form method="POST" action="{{ route('update-request.reject', $request->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700"
                            onclick="return confirm('Are you sure you want to reject this request?')">
                        Reject Request
                    </button>
                </form>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">No new requests</h3>
                <p class="text-sm text-gray-600">There are no pending update requests at this time.</p>
            </div>
        </div>
        @endforelse
    </div>
</x-app-layout>
