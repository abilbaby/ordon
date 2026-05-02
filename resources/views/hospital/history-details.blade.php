<x-app-layout>
    <x-slot name="title">History Details</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">History Details</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $request->recipient->user->name }} - {{ $request->status }} Request</p>
                </div>
                <a href="{{ route('update-history') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                    ← Back to History
                </a>
            </div>
        </div>

        <!-- Request Information -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ $request->recipient->user->name }}</h2>
                    <p class="text-sm text-gray-600">Submitted: {{ $request->created_at?->format('Y-m-d H:i') }}</p>
                    @if ($request->reviewed_at)
                        <p class="text-xs text-gray-500">Reviewed: {{ $request->reviewed_at?->format('Y-m-d H:i') }}</p>
                    @endif
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $request->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $request->status }}
                </span>
            </div>

            @if ($request->reason)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Reason for Change:</h3>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-md">{{ $request->reason }}</p>
                </div>
            @endif

            @if ($request->reviewer_note)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Reviewer Note:</h3>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-md">{{ $request->reviewer_note }}</p>
                </div>
            @endif

            @if ($request->status === 'Approved' && !empty($approvedFields))
                <!-- Approved Fields -->
                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-4">Approved Changes:</h3>
                    <div class="space-y-4">
                        @if (in_array('full_name', $approvedFields))
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Full Name</p>
                                    <p class="text-xs text-gray-500">Old: {{ $request->recipient->user->name }}</p>
                                    <p class="text-xs text-green-600">New: {{ $request->requested_full_name }}</p>
                                </div>
                            </div>
                        @endif

                        @if (in_array('dob', $approvedFields))
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Date of Birth</p>
                                    <p class="text-xs text-gray-500">Old: {{ $request->recipient->date_of_birth?->format('Y-m-d') }}</p>
                                    <p class="text-xs text-green-600">New: {{ $request->requested_dob }}</p>
                                </div>
                            </div>
                        @endif

                        @if (in_array('gender', $approvedFields))
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Gender</p>
                                    <p class="text-xs text-gray-500">Old: {{ ucfirst($request->recipient->gender ?? '') }}</p>
                                    <p class="text-xs text-green-600">New: {{ ucfirst($request->requested_gender) }}</p>
                                </div>
                            </div>
                        @endif

                        @if (in_array('blood_group', $approvedFields))
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Blood Group</p>
                                    <p class="text-xs text-gray-500">Old: {{ $request->recipient->blood_group }}</p>
                                    <p class="text-xs text-green-600">New: {{ $request->requested_blood_group }}</p>
                                </div>
                            </div>
                        @endif

                        @if (in_array('organ', $approvedFields))
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Organ Needed</p>
                                    <p class="text-xs text-gray-500">Old: {{ $request->recipient->organ_needed }}</p>
                                    <p class="text-xs text-green-600">New: {{ $request->requested_organ_needed }}</p>
                                </div>
                            </div>
                        @endif

                        @if (in_array('urgency', $approvedFields))
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Urgency Level</p>
                                    <p class="text-xs text-gray-500">Old: {{ ucfirst($request->recipient->urgency_level ?? '') }}</p>
                                    <p class="text-xs text-green-600">New: {{ ucfirst($request->requested_urgency_level) }}</p>
                                </div>
                            </div>
                        @endif

                        @if (in_array('waiting_time', $approvedFields))
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Waiting Time</p>
                                    <p class="text-xs text-gray-500">Old: {{ $request->recipient->waiting_time }} days</p>
                                    <p class="text-xs text-green-600">New: {{ $request->requested_waiting_time }} days</p>
                                </div>
                            </div>
                        @endif

                        @if (in_array('other_organs', $approvedFields))
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Other Organs Needed</p>
                                    <p class="text-xs text-gray-500">Old: {{ is_array($request->recipient->organs_needed) ? implode(', ', $request->recipient->organs_needed) : 'None' }}</p>
                                    <p class="text-xs text-green-600">New: {{ is_array(json_decode($request->requested_other_organs)) ? implode(', ', json_decode($request->requested_other_organs)) : $request->requested_other_organs }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($request->status === 'Rejected')
                <!-- Rejected Request -->
                <div>
                    <h3 class="text-base font-medium text-gray-900 mb-4">Requested Changes (Rejected):</h3>
                    <div class="space-y-4">
                        @if ($request->requested_full_name && $request->requested_full_name !== $request->recipient->user->name)
                            <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Full Name</p>
                                    <p class="text-xs text-gray-500">Current: {{ $request->recipient->user->name }}</p>
                                    <p class="text-xs text-red-600">Requested: {{ $request->requested_full_name }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($request->requested_dob && $request->requested_dob !== $request->recipient->date_of_birth)
                            <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Date of Birth</p>
                                    <p class="text-xs text-gray-500">Current: {{ $request->recipient->date_of_birth?->format('Y-m-d') }}</p>
                                    <p class="text-xs text-red-600">Requested: {{ $request->requested_dob }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($request->requested_gender && $request->requested_gender !== $request->recipient->gender)
                            <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Gender</p>
                                    <p class="text-xs text-gray-500">Current: {{ ucfirst($request->recipient->gender ?? '') }}</p>
                                    <p class="text-xs text-red-600">Requested: {{ ucfirst($request->requested_gender) }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($request->requested_blood_group && $request->requested_blood_group !== $request->recipient->blood_group)
                            <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Blood Group</p>
                                    <p class="text-xs text-gray-500">Current: {{ $request->recipient->blood_group }}</p>
                                    <p class="text-xs text-red-600">Requested: {{ $request->requested_blood_group }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($request->requested_organ_needed && $request->requested_organ_needed !== $request->recipient->organ_needed)
                            <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Organ Needed</p>
                                    <p class="text-xs text-gray-500">Current: {{ $request->recipient->organ_needed }}</p>
                                    <p class="text-xs text-red-600">Requested: {{ $request->requested_organ_needed }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($request->requested_urgency_level && $request->requested_urgency_level !== $request->recipient->urgency_level)
                            <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Urgency Level</p>
                                    <p class="text-xs text-gray-500">Current: {{ ucfirst($request->recipient->urgency_level ?? '') }}</p>
                                    <p class="text-xs text-red-600">Requested: {{ ucfirst($request->requested_urgency_level) }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($request->requested_waiting_time && $request->requested_waiting_time !== $request->recipient->waiting_time)
                            <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Waiting Time</p>
                                    <p class="text-xs text-gray-500">Current: {{ $request->recipient->waiting_time }} days</p>
                                    <p class="text-xs text-red-600">Requested: {{ $request->requested_waiting_time }} days</p>
                                </div>
                            </div>
                        @endif

                        @if ($request->requested_other_organs && json_decode($request->requested_other_organs) !== $request->recipient->organs_needed)
                            <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Other Organs Needed</p>
                                    <p class="text-xs text-gray-500">Current: {{ is_array($request->recipient->organs_needed) ? implode(', ', $request->recipient->organs_needed) : 'None' }}</p>
                                    <p class="text-xs text-red-600">Requested: {{ is_array(json_decode($request->requested_other_organs)) ? implode(', ', json_decode($request->requested_other_organs)) : $request->requested_other_organs }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Review Information -->
            @if ($request->reviewed_by)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Reviewed By:</h3>
                    <p class="text-sm text-gray-600">{{ $request->reviewedBy?->name ?? 'Unknown' }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
