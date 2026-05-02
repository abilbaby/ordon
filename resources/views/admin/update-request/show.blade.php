<x-app-layout>
    <x-slot name="title">Review Update Request #{{ $request->id }}</x-slot>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Review Update Request</h2>
                <p class="text-sm text-gray-600">Request ID: #{{ $request->id }} | Submitted: {{ $request->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Field-Level Review Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-base font-medium text-gray-900 mb-4">Field-Level Review</h3>
        
        <form method="POST" action="{{ route('update.request.approve', $request->id) }}" x-data="{ selectAll: false }">
            @csrf
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 w-16">
                                <input type="checkbox" x-model="selectAll" @change="document.querySelectorAll('input[name=\"approvedFields[]\"]').forEach(cb => cb.checked = selectAll)" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Field</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Current Value</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Requested Value</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Full Name -->
                        <tr class="border-b border-gray-100 {{ $request->requested_full_name !== ($currentValues['full_name'] ?? '') ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="approvedFields[]" value="full_name" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       @if($request->requested_full_name !== ($currentValues['full_name'] ?? '')) checked @endif>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">Full Name</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $currentValues['full_name'] ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $request->requested_full_name }}</td>
                            <td class="py-3 px-4">
                                @if($request->requested_full_name !== ($currentValues['full_name'] ?? ''))
                                    <span class="text-xs text-blue-600 font-medium">Changed</span>
                                @else
                                    <span class="text-xs text-gray-400">No change</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Date of Birth -->
                        <tr class="border-b border-gray-100 {{ $request->requested_dob?->format('Y-m-d') !== ($currentValues['date_of_birth'] ?? '') ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="approvedFields[]" value="date_of_birth" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       @if($request->requested_dob?->format('Y-m-d') !== ($currentValues['date_of_birth'] ?? '')) checked @endif>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">Date of Birth</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $currentValues['date_of_birth'] ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $request->requested_dob?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td class="py-3 px-4">
                                @if($request->requested_dob?->format('Y-m-d') !== ($currentValues['date_of_birth'] ?? ''))
                                    <span class="text-xs text-blue-600 font-medium">Changed</span>
                                @else
                                    <span class="text-xs text-gray-400">No change</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Gender -->
                        <tr class="border-b border-gray-100 {{ $request->requested_gender !== ($currentValues['gender'] ?? '') ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="approvedFields[]" value="gender" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       @if($request->requested_gender !== ($currentValues['gender'] ?? '')) checked @endif>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">Gender</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ ucfirst($currentValues['gender'] ?? 'N/A') }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ ucfirst($request->requested_gender ?? 'N/A') }}</td>
                            <td class="py-3 px-4">
                                @if($request->requested_gender !== ($currentValues['gender'] ?? ''))
                                    <span class="text-xs text-blue-600 font-medium">Changed</span>
                                @else
                                    <span class="text-xs text-gray-400">No change</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Blood Group -->
                        <tr class="border-b border-gray-100 {{ $request->requested_blood_group !== ($currentValues['blood_group'] ?? '') ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="approvedFields[]" value="blood_group" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       @if($request->requested_blood_group !== ($currentValues['blood_group'] ?? '')) checked @endif>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">Blood Group</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $currentValues['blood_group'] ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $request->requested_blood_group }}</td>
                            <td class="py-3 px-4">
                                @if($request->requested_blood_group !== ($currentValues['blood_group'] ?? ''))
                                    <span class="text-xs text-blue-600 font-medium">Changed</span>
                                @else
                                    <span class="text-xs text-gray-400">No change</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Organ Needed -->
                        <tr class="border-b border-gray-100 {{ $request->requested_organ_needed !== ($currentValues['organ_needed'] ?? '') ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="approvedFields[]" value="organ_needed" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       @if($request->requested_organ_needed !== ($currentValues['organ_needed'] ?? '')) checked @endif>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">Organ Needed</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $currentValues['organ_needed'] ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $request->requested_organ_needed }}</td>
                            <td class="py-3 px-4">
                                @if($request->requested_organ_needed !== ($currentValues['organ_needed'] ?? ''))
                                    <span class="text-xs text-blue-600 font-medium">Changed</span>
                                @else
                                    <span class="text-xs text-gray-400">No change</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Urgency Level -->
                        <tr class="border-b border-gray-100 {{ $request->requested_urgency_level !== ($currentValues['urgency_level'] ?? '') ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="approvedFields[]" value="urgency_level" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       @if($request->requested_urgency_level !== ($currentValues['urgency_level'] ?? '')) checked @endif>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">Urgency Level</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ ucfirst($currentValues['urgency_level'] ?? 'N/A') }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ ucfirst($request->requested_urgency_level) }}</td>
                            <td class="py-3 px-4">
                                @if($request->requested_urgency_level !== ($currentValues['urgency_level'] ?? ''))
                                    <span class="text-xs text-blue-600 font-medium">Changed</span>
                                @else
                                    <span class="text-xs text-gray-400">No change</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Waiting Time -->
                        <tr class="border-b border-gray-100 {{ $request->requested_waiting_time !== ($currentValues['waiting_time'] ?? 0) ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="approvedFields[]" value="waiting_time" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       @if($request->requested_waiting_time !== ($currentValues['waiting_time'] ?? 0)) checked @endif>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">Waiting Time (days)</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $currentValues['waiting_time'] ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $request->requested_waiting_time }}</td>
                            <td class="py-3 px-4">
                                @if($request->requested_waiting_time !== ($currentValues['waiting_time'] ?? 0))
                                    <span class="text-xs text-blue-600 font-medium">Changed</span>
                                @else
                                    <span class="text-xs text-gray-400">No change</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Other Organs -->
                        <tr class="{{ $request->requested_other_organs != ($currentValues['organs_needed'] ?? []) ? 'bg-blue-50' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="approvedFields[]" value="other_organs" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       @if($request->requested_other_organs != ($currentValues['organs_needed'] ?? [])) checked @endif>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">Other Organs Needed</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ is_array($currentValues['organs_needed']) ? implode(', ', $currentValues['organs_needed']) : 'None' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ is_array($request->requested_other_organs) ? implode(', ', $request->requested_other_organs) : 'None' }}</td>
                            <td class="py-3 px-4">
                                @if($request->requested_other_organs != ($currentValues['organs_needed'] ?? []))
                                    <span class="text-xs text-blue-600 font-medium">Changed</span>
                                @else
                                    <span class="text-xs text-gray-400">No change</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Reason for change -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Reason for Change</h4>
                <p class="text-sm text-gray-700">{{ $request->reason }}</p>
            </div>

            <!-- Final Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Approve Selected -->
                    <div class="flex-1">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            Approve Selected Changes
                        </button>
                        <p class="text-xs text-gray-500 mt-1">Only checked fields will be updated</p>
                    </div>

                    <!-- Approve All -->
                    <div class="flex-1">
                        <form method="POST" action="{{ route('update.request.approve', $request->id) }}" class="inline">
                            @csrf
                            <input type="hidden" name="approve_all" value="1">
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                Approve All Changes
                            </button>
                            <p class="text-xs text-gray-500 mt-1">All fields will be updated</p>
                        </form>
                    </div>

                    <!-- Reject -->
                    <div class="flex-1">
                        <button type="button" onclick="document.getElementById('rejectForm').style.display = 'block'" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                            Reject Request
                        </button>
                        <p class="text-xs text-gray-500 mt-1">No changes will be applied</p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Reject Form (Hidden by default) -->
    <form id="rejectForm" method="POST" action="{{ route('update.request.reject', $request->id) }}" class="hidden bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        @csrf
        <h3 class="text-base font-medium text-gray-900 mb-4">Reject Request</h3>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason (Required)</label>
            <textarea name="note" rows="3" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="Please explain why this request is being rejected..."></textarea>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                Reject Request
            </button>
            <button type="button" onclick="document.getElementById('rejectForm').style.display = 'none'" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancel
            </button>
        </div>
    </form>
</x-app-layout>