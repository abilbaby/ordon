<x-app-layout>
    <x-slot name="title">Request Details</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Request Details</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $request->recipient->user->name }} - Update Request</p>
                </div>
                <a href="{{ route('update-requests') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                    ← Back to Requests
                </a>
            </div>
        </div>

        <!-- Request Information -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ $request->recipient->user->name }}</h2>
                    <p class="text-sm text-gray-600">Submitted: {{ $request->created_at?->format('Y-m-d H:i') }}</p>
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                    Pending
                </span>
            </div>

            @if ($request->reason)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Reason for Change:</h3>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-md">{{ $request->reason }}</p>
                </div>
            @endif

            <!-- Field Selection Form -->
            <form method="POST" action="{{ route('update-request.approve-selected', $request->id) }}">
                @csrf
                
                <h3 class="text-base font-medium text-gray-900 mb-4">Select Fields to Approve:</h3>
                
                <div class="space-y-4 mb-6">
                    <!-- Identity Fields -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Identity Information</h4>
                        <div class="space-y-3">
                            @if ($request->requested_full_name && $request->requested_full_name !== $request->recipient->user->name)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox" name="approvedFields[]" value="full_name" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Full Name</p>
                                            <p class="text-xs text-gray-500">Current: {{ $request->recipient->user->name }}</p>
                                            <p class="text-xs text-green-600">Requested: {{ $request->requested_full_name }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($request->requested_dob && $request->requested_dob !== $request->recipient->date_of_birth)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox" name="approvedFields[]" value="dob" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Date of Birth</p>
                                            <p class="text-xs text-gray-500">Current: {{ $request->recipient->date_of_birth?->format('Y-m-d') }}</p>
                                            <p class="text-xs text-green-600">Requested: {{ $request->requested_dob }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($request->requested_gender && $request->requested_gender !== $request->recipient->gender)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox" name="approvedFields[]" value="gender" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Gender</p>
                                            <p class="text-xs text-gray-500">Current: {{ ucfirst($request->recipient->gender ?? '') }}</p>
                                            <p class="text-xs text-green-600">Requested: {{ ucfirst($request->requested_gender) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Medical Fields -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Medical Information</h4>
                        <div class="space-y-3">
                            @if ($request->requested_blood_group && $request->requested_blood_group !== $request->recipient->blood_group)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox" name="approvedFields[]" value="blood_group" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Blood Group</p>
                                            <p class="text-xs text-gray-500">Current: {{ $request->recipient->blood_group }}</p>
                                            <p class="text-xs text-green-600">Requested: {{ $request->requested_blood_group }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($request->requested_organ_needed && $request->requested_organ_needed !== $request->recipient->organ_needed)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox" name="approvedFields[]" value="organ" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Organ Needed</p>
                                            <p class="text-xs text-gray-500">Current: {{ $request->recipient->organ_needed }}</p>
                                            <p class="text-xs text-green-600">Requested: {{ $request->requested_organ_needed }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($request->requested_urgency_level && $request->requested_urgency_level !== $request->recipient->urgency_level)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox" name="approvedFields[]" value="urgency" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Urgency Level</p>
                                            <p class="text-xs text-gray-500">Current: {{ ucfirst($request->recipient->urgency_level ?? '') }}</p>
                                            <p class="text-xs text-green-600">Requested: {{ ucfirst($request->requested_urgency_level) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($request->requested_waiting_time && $request->requested_waiting_time !== $request->recipient->waiting_time)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox" name="approvedFields[]" value="waiting_time" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Waiting Time</p>
                                            <p class="text-xs text-gray-500">Current: {{ $request->recipient->waiting_time }} days</p>
                                            <p class="text-xs text-green-600">Requested: {{ $request->requested_waiting_time }} days</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($request->requested_other_organs && json_decode($request->requested_other_organs) !== $request->recipient->organs_needed)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox" name="approvedFields[]" value="other_organs" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Other Organs Needed</p>
                                            <p class="text-xs text-gray-500">Current: {{ is_array($request->recipient->organs_needed) ? implode(', ', $request->recipient->organs_needed) : 'None' }}</p>
                                            <p class="text-xs text-green-600">Requested: {{ is_array(json_decode($request->requested_other_organs)) ? implode(', ', json_decode($request->requested_other_organs)) : $request->requested_other_organs }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                        Approve Selected Fields
                    </button>
                    <form method="POST" action="{{ route('update-request.reject', $request->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to reject this request?')">
                            Reject Request
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
