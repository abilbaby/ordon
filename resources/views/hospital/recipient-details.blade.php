<x-app-layout>
    <x-slot name="title">Recipient Details - {{ $recipient->user->name }}</x-slot>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Recipient Details</h2>
                <p class="text-sm text-gray-600">Recipient ID: #{{ $recipient->id }}</p>
            </div>
            <a href="{{ route('hospital.recipients') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                ← Back to Recipients
            </a>
        </div>
    </div>

    <!-- SECTION A: Personal Information -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-base font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Personal Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" value="{{ $recipient->user->name }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" value="{{ $recipient->phone }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea rows="2" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>{{ $recipient->address }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                <input type="date" value="{{ $recipient->date_of_birth?->format('Y-m-d') }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                <input type="text" value="{{ ucfirst($recipient->gender ?? '') }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
        </div>
    </div>

    <!-- SECTION B: Medical Information -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-base font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Medical Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                <input type="text" value="{{ $recipient->blood_group }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Organ Needed</label>
                <input type="text" value="{{ $recipient->organ_needed }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urgency Level</label>
                <input type="text" value="{{ ucfirst($recipient->urgency_level ?? '') }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Waiting Time (days)</label>
                <input type="number" value="{{ $recipient->waiting_time }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Other Organs Needed</label>
                <input type="text" value="{{ is_array($recipient->organs_needed) ? implode(', ', $recipient->organs_needed) : '' }}" 
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
            </div>
        </div>
    </div>

    <!-- SECTION C: Activity / History -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h3 class="text-base font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Activity / History</h3>
        
        @if ($updateHistory->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Field Changed</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Old Value</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">New Value</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Changed By</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($updateHistory as $change)
                            <tr class="border-b border-gray-100">
                                <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $change['field_name'] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $change['old_value'] ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $change['new_value'] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $change['changed_by'] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $change['created_at']->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500">No update history found for this recipient.</p>
            </div>
        @endif
    </div>
</x-app-layout>
