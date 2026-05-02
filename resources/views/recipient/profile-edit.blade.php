<x-app-layout>
    <x-slot name="title">Edit Profile</x-slot>

    <div class="card-pro mb-6">
        <h3 class="text-lg font-semibold mb-2">Personal Information</h3>
        <p class="text-sm text-slate-600">
            Update your personal details below. Your identity number cannot be changed.
        </p>
    </div>

    <div class="card-pro">
        <form method="POST" action="{{ route('recipient.profile.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            @method('PATCH')
            
            <!-- Identity Number (Read-only) -->
            <div class="md:col-span-2">
                <label class="text-sm text-slate-600">Identity Number</label>
                <input type="text" value="{{ $recipient->masked_identity }}" class="mt-1 form-control bg-slate-100" readonly>
                <p class="mt-1 text-xs text-slate-500">Identity number cannot be changed.</p>
            </div>

            <!-- Full Name -->
            <div>
                <label class="text-sm text-slate-600">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name', $user->name) }}" 
                    class="mt-1 form-control @error('full_name') form-control-invalid @enderror"
                    minlength="2" maxlength="100" pattern="[a-zA-Z\s]+" required>
                @error('full_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label class="text-sm text-slate-600">Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone', $recipient->phone) }}" 
                    class="mt-1 form-control @error('phone') form-control-invalid @enderror"
                    minlength="10" maxlength="15" pattern="[0-9]{10,15}" placeholder="10-15 digits">
                @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Date of Birth -->
            <div>
                <label class="text-sm text-slate-600">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $recipient->date_of_birth?->format('Y-m-d')) }}" 
                    class="mt-1 form-control @error('date_of_birth') form-control-invalid @enderror"
                    max="{{ now()->subDays(1)->format('Y-m-d') }}">
                @error('date_of_birth') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Gender -->
            <div>
                <label class="text-sm text-slate-600">Gender</label>
                <select name="gender" class="mt-1 form-control @error('gender') form-control-invalid @enderror">
                    <option value="">Select Gender</option>
                    <option value="male" @selected(old('gender', $recipient->gender) === 'male')>Male</option>
                    <option value="female" @selected(old('gender', $recipient->gender) === 'female')>Female</option>
                    <option value="other" @selected(old('gender', $recipient->gender) === 'other')>Other</option>
                </select>
                @error('gender') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Blood Group -->
            <div>
                <label class="text-sm text-slate-600">Blood Group</label>
                <select name="blood_group" class="mt-1 form-control @error('blood_group') form-control-invalid @enderror">
                    <option value="">Select Blood Group</option>
                    @foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $group)
                        <option value="{{ $group }}" @selected(old('blood_group', $recipient->blood_group) === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                @error('blood_group') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Address -->
            <div class="md:col-span-2">
                <label class="text-sm text-slate-600">Address</label>
                <textarea name="address" rows="2" class="mt-1 form-control @error('address') form-control-invalid @enderror"
                    minlength="5" maxlength="255">{{ old('address', $recipient->address) }}</textarea>
                @error('address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Emergency Contact Name -->
            <div>
                <label class="text-sm text-slate-600">Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $recipient->emergency_contact_name) }}" 
                    class="mt-1 form-control @error('emergency_contact_name') form-control-invalid @enderror"
                    minlength="2" maxlength="100">
                @error('emergency_contact_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Emergency Contact Phone -->
            <div>
                <label class="text-sm text-slate-600">Emergency Contact Phone</label>
                <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $recipient->emergency_contact_phone) }}" 
                    class="mt-1 form-control @error('emergency_contact_phone') form-control-invalid @enderror"
                    minlength="10" maxlength="15" pattern="[0-9]{10,15}" placeholder="10-15 digits">
                @error('emergency_contact_phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex gap-3">
                <button type="submit" class="rounded-xl bg-slate-900 text-white px-4 py-2.5 font-semibold disabled:cursor-not-allowed disabled:opacity-60" :disabled="submitting">
                    <span x-show="!submitting">Save Changes</span>
                    <span x-show="submitting">Saving...</span>
                </button>
                <a href="{{ route('recipient.dashboard') }}" class="rounded-xl border border-slate-300 text-slate-700 px-4 py-2.5 font-semibold hover:bg-slate-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Current Status -->
    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Account Status</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-xl border border-[#d7e8f4] p-3 text-center">
                <p class="text-xs text-slate-500">Identity Verified</p>
                <p class="text-lg font-semibold {{ $recipient->identity_verified ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $recipient->identity_verified ? 'Yes' : 'No' }}
                </p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] p-3 text-center">
                <p class="text-xs text-slate-500">Hospital Verified</p>
                <p class="text-lg font-semibold {{ $recipient->hospital_verified ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $recipient->hospital_verified ? 'Yes' : 'Pending' }}
                </p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] p-3 text-center">
                <p class="text-xs text-slate-500">Admin Approved</p>
                <p class="text-lg font-semibold {{ $recipient->admin_approved ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $recipient->admin_approved ? 'Yes' : 'Pending' }}
                </p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] p-3 text-center">
                <p class="text-xs text-slate-500">Account Status</p>
                <p class="text-lg font-semibold text-slate-700">{{ $recipient->status }}</p>
            </div>
        </div>
    </div>
</x-app-layout>