<x-guest-layout>
    <h2 class="text-xl font-semibold text-slate-900 mb-1">Recipient Invitation Registration</h2>
    <p class="text-sm text-slate-600 mb-5">Complete your registration using the secure invitation from the hospital.</p>

    <form method="POST" action="{{ route('recipient.invite.register.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="rvid" value="{{ $invite->rvid }}">

        <div>
            <label class="block text-sm text-slate-600 mb-1">Name</label>
            <input value="{{ $invite->recipient_name }}" class="w-full rounded-xl border-slate-200 bg-slate-100" readonly>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Email</label>
            <input value="{{ $invite->email }}" class="w-full rounded-xl border-slate-200 bg-slate-100" readonly>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Blood Group</label>
            <input value="{{ $invite->blood_group }}" class="w-full rounded-xl border-slate-200 bg-slate-100" readonly>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Government ID Type</label>
            <select name="identity_type" class="w-full rounded-xl border-slate-200" required>
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
            <input name="identity_number" value="{{ old('identity_number') }}" class="w-full rounded-xl border-slate-200" required>
            @error('identity_number') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-slate-500 mt-1">For verification only. This link remains valid for 24 hours and can be used once.</p>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Password</label>
            <input type="password" name="password" class="w-full rounded-xl border-slate-200" required>
            @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full rounded-xl border-slate-200" required>
        </div>
        <button class="w-full rounded-xl bg-slate-900 text-white py-2.5">Register</button>
    </form>
</x-guest-layout>
