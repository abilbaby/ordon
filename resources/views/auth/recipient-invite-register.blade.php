<x-guest-layout>
    <h2 class="text-xl font-semibold text-slate-900 mb-1">Recipient Invitation Registration</h2>
    <p class="text-sm text-slate-600 mb-5">Complete your registration using the secure invitation from the hospital.</p>

    <form method="POST" action="{{ route('recipient.invite.register.store') }}" class="space-y-4"
        x-data="{
            submitting: false,
            identityType: '{{ old('identity_type') }}',
            rules: {
                aadhaar: { maxlength: 12, pattern: '\\d{12}', inputmode: 'numeric', placeholder: '123456789012' },
                pan: { maxlength: 10, pattern: '[A-Za-z]{5}[0-9]{4}[A-Za-z]', inputmode: 'text', placeholder: 'ABCDE1234F' },
                passport: { maxlength: 9, pattern: '[A-Za-z0-9]{6,9}', inputmode: 'text', placeholder: 'A1234567' },
                driving_licence: { maxlength: 16, pattern: '[A-Za-z0-9]{10,16}', inputmode: 'text', placeholder: 'DL0420110012345' },
                voter_id: { maxlength: 10, pattern: '[A-Za-z0-9]{10}', inputmode: 'text', placeholder: 'ABC1234567' },
                other: { maxlength: 20, pattern: '[A-Za-z0-9]{5,20}', inputmode: 'text', placeholder: 'Enter ID number' }
            },
            currentRule() { return this.rules[this.identityType] || { maxlength: 20, pattern: '[A-Za-z0-9]{5,20}', inputmode: 'text', placeholder: 'Select ID type first' }; }
        }"
        @submit="submitting = true">
        @csrf
        <input type="hidden" name="rvid" value="{{ $invite->rvid }}">

        <div>
            <label class="block text-sm text-slate-600 mb-1">Name</label>
            <input value="{{ $invite->recipient_name }}" class="form-control bg-slate-100" readonly>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Email</label>
            <input value="{{ $invite->email }}" class="form-control bg-slate-100" readonly>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Blood Group</label>
            <input value="{{ $invite->blood_group }}" class="form-control bg-slate-100" readonly>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Government ID Type</label>
            <select name="identity_type" x-model="identityType" class="form-control @error('identity_type') form-control-invalid @enderror" required>
                <option value="">Select ID Type</option>
                <option value="aadhaar" @selected(old('identity_type') === 'aadhaar')>Aadhaar</option>
                <option value="passport" @selected(old('identity_type') === 'passport')>Passport</option>
                <option value="voter_id" @selected(old('identity_type') === 'voter_id')>Voter ID</option>
                <option value="driving_licence" @selected(old('identity_type') === 'driving_licence')>Driving Licence</option>
                <option value="pan" @selected(old('identity_type') === 'pan')>PAN</option>
                <option value="other" @selected(old('identity_type') === 'other')>Other</option>
            </select>
            @error('identity_type') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Government ID Number</label>
            <input name="identity_number" value="{{ old('identity_number') }}" class="form-control uppercase @error('identity_number') form-control-invalid @enderror"
                required :maxlength="currentRule().maxlength" :pattern="currentRule().pattern" :inputmode="currentRule().inputmode"
                :placeholder="currentRule().placeholder" @input="$el.value = $el.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()">
            @error('identity_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-slate-500 mt-1">For verification only. This link remains valid for 24 hours and can be used once.</p>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Password</label>
            <input type="password" name="password" class="form-control @error('password') form-control-invalid @enderror" required minlength="8" autocomplete="new-password">
            @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
        </div>
        <button class="w-full rounded-xl bg-slate-900 text-white py-2.5 font-semibold disabled:cursor-not-allowed disabled:opacity-60" :disabled="submitting">
            <span x-show="!submitting">Register</span>
            <span x-show="submitting">Registering...</span>
        </button>
    </form>
</x-guest-layout>
