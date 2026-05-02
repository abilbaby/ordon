<x-app-layout>
    <x-slot name="title">Recipient Profile</x-slot>

    <style>
        .form-action-wrapper {
            display: block;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            text-align: center;
            vertical-align: middle;
            line-height: 1.5;
        }

        .btn-primary {
            background: #1f2937;
            color: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .btn-primary:hover {
            background: #111827;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            background: #374151;
            transform: translateY(0);
        }

        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Ensure proper layout */
        form {
            display: block;
        }

        .bg-white {
            overflow: visible;
        }
    </style>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h1 class="text-xl font-semibold text-gray-900">Recipient Profile</h1>
            <p class="text-sm text-gray-600 mt-1">Manage your personal and medical information</p>
        </div>

        <!-- SECTION 1: Personal Details -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-6">Personal Details</h2>
            
            <form method="POST" action="{{ route('recipient.update.direct-fields') }}" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Approval Required Fields -->
                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" value="{{ $recipient->user->name }}" 
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" 
                            readonly disabled>
                        <p class="mt-1 text-xs text-gray-400">Requires hospital approval</p>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" value="{{ $recipient->date_of_birth?->format('Y-m-d') }}" 
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" 
                            readonly disabled>
                        <p class="mt-1 text-xs text-gray-400">Requires hospital approval</p>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <input type="text" value="{{ ucfirst($recipient->gender ?? '') }}" 
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" 
                            readonly disabled>
                        <p class="mt-1 text-xs text-gray-400">Requires hospital approval</p>
                    </div>

                    <!-- Directly Editable Fields -->
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $recipient->phone) }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            minlength="10" maxlength="15" pattern="[0-9]{10,15}" placeholder="10-15 digits">
                        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            minlength="5" maxlength="255">{{ old('address', $recipient->address) }}</textarea>
                        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Emergency Contact Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $recipient->emergency_contact_name) }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            minlength="2" maxlength="100">
                        @error('emergency_contact_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Emergency Contact Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $recipient->emergency_contact_phone) }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            minlength="10" maxlength="15" pattern="[0-9]{10,15}" placeholder="10-15 digits">
                        @error('emergency_contact_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-action-wrapper">
                    <button type="submit" class="btn btn-primary" :disabled="submitting">
                        <span x-show="!submitting">Save Changes</span>
                        <span x-show="submitting">Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- SECTION 2: Medical Details (Restricted) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-6">Medical Details (Restricted)</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Blood Group -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                    <input type="text" value="{{ $recipient->blood_group }}" 
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" 
                        readonly disabled>
                </div>

                <!-- Organ Needed -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Organ Needed</label>
                    <input type="text" value="{{ $recipient->organ_needed }}" 
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" 
                        readonly disabled>
                </div>

                <!-- Urgency Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urgency Level</label>
                    <input type="text" value="{{ ucfirst($recipient->urgency_level ?? '') }}" 
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" 
                        readonly disabled>
                </div>

                <!-- Waiting Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Waiting Time (days)</label>
                    <input type="number" value="{{ $recipient->waiting_time }}" 
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" 
                        readonly disabled>
                </div>

                <!-- Other Organs Needed -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Other Organs Needed</label>
                    <input type="text" value="{{ is_array($recipient->organs_needed) ? implode(', ', $recipient->organs_needed) : '' }}" 
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-gray-500" 
                        readonly disabled>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">These details are managed by the hospital and require approval for changes</p>
        </div>

        <!-- SECTION 3: Request Change -->
        @if (!$pendingRequest)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-6">Request Changes (Approval Required)</h2>
            
            <form method="POST" action="{{ route('recipient.update.request.submit') }}" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                
                <div class="space-y-8">
                    <!-- Identity Changes -->
                    <div>
                        <h3 class="text-base font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Identity Changes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    minlength="2" maxlength="100" placeholder="Enter new name">
                                @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    max="{{ now()->subDays(1)->format('Y-m-d') }}">
                                @error('date_of_birth') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Gender</option>
                                    <option value="male" @selected(old('gender') === 'male')">Male</option>
                                    <option value="female" @selected(old('gender') === 'female')">Female</option>
                                    <option value="other" @selected(old('gender') === 'other')">Other</option>
                                </select>
                                @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Medical Changes -->
                    <div>
                        <h3 class="text-base font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Medical Changes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Blood Group -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                                <select name="blood_group" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Blood Group</option>
                                    @foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $group)
                                        <option value="{{ $group }}" @selected(old('blood_group') === $group)>{{ $group }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Organ Needed -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Organ Needed</label>
                                <input type="text" name="organ_needed" value="{{ old('organ_needed') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    maxlength="255" placeholder="Enter organ needed">
                                @error('organ_needed') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Urgency Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Urgency Level</label>
                                <select name="urgency_level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Urgency</option>
                                    @foreach (['high','medium','low'] as $urgency)
                                        <option value="{{ $urgency }}" @selected(old('urgency_level') === $urgency)>{{ strtoupper($urgency) }}</option>
                                    @endforeach
                                </select>
                                @error('urgency_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Waiting Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Waiting Time (days)</label>
                                <input type="number" name="waiting_time" value="{{ old('waiting_time') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    min="0" max="3650" placeholder="Enter waiting time">
                                @error('waiting_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Other Organs Needed -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Other Organs Needed</label>
                                <input type="text" name="other_organs_needed" value="{{ old('other_organs_needed') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Kidney, Liver" maxlength="255">
                                @error('other_organs_needed') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Reason for change -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for change <span class="text-red-500">*</span></label>
                        <textarea name="reason" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            maxlength="1000" required placeholder="Please explain why these changes are needed...">{{ old('reason') }}</textarea>
                        @error('reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-action-wrapper">
                    <button type="submit" class="btn btn-primary" :disabled="submitting">
                        <span x-show="!submitting">Submit Change Request</span>
                        <span x-show="submitting">Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
        @else
        <!-- Pending Request Notice -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 text-amber-600">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-medium text-amber-800">Request Under Review</h3>
                    <p class="text-sm text-amber-700">You already have a pending request under review. You cannot submit new changes until the current request is processed.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Requests -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Requests</h2>
            <div class="space-y-2">
                @forelse ($recentRequests as $item)
                    <div class="flex items-center justify-between py-3 px-4 border border-gray-100 rounded-md">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                @php
                                    $changes = [];
                                    if ($item->requested_full_name && $item->requested_full_name !== $item->recipient->user->name) $changes[] = 'Name';
                                    if ($item->requested_blood_group && $item->requested_blood_group !== $item->recipient->blood_group) $changes[] = 'Blood Group';
                                    if ($item->requested_organ_needed && $item->requested_organ_needed !== $item->recipient->organ_needed) $changes[] = 'Organ';
                                    if ($item->requested_urgency_level && $item->requested_urgency_level !== $item->recipient->urgency_level) $changes[] = 'Urgency';
                                    echo !empty($changes) ? implode(', ', $changes) : 'Multiple fields';
                                @endphp
                            </p>
                            <p class="text-xs text-gray-500">{{ $item->created_at?->format('Y-m-d H:i') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $item->status === 'Approved' ? 'bg-green-100 text-green-800' : ($item->status === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $item->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No update requests submitted yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
