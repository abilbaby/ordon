<x-guest-layout>
    <h1 class="text-2xl font-semibold text-slate-900 mb-1">Create your ORDON account</h1>
    <p class="text-sm text-slate-500 mb-2">Join as donor, recipient, or hospital partner.</p>
    <p class="text-xs text-slate-500 mb-6">Only essential onboarding details are collected here. Medical/profile details can be added later from your dashboard.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select role</option>
                <option value="donor" @selected(old('role') === 'donor')>Donor</option>
                <option value="recipient" @selected(old('role') === 'recipient')>Recipient</option>
                <option value="hospital" @selected(old('role') === 'hospital')>Hospital</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">Admin accounts are created internally only.</p>
        </div>

        <div class="mt-4">
            <x-input-label for="identity_type" :value="__('Identity Type')" />
            <select id="identity_type" name="identity_type" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select identity type</option>
                <option value="aadhaar" @selected(old('identity_type') === 'aadhaar')>Aadhaar</option>
                <option value="passport" @selected(old('identity_type') === 'passport')>Passport</option>
                <option value="voter_id" @selected(old('identity_type') === 'voter_id')>Voter ID</option>
                <option value="driving_licence" @selected(old('identity_type') === 'driving_licence')>Driving Licence</option>
                <option value="pan" @selected(old('identity_type') === 'pan')>PAN</option>
                <option value="other" @selected(old('identity_type') === 'other')>Other</option>
            </select>
            <x-input-error :messages="$errors->get('identity_type')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="identity_number" :value="__('Identity Number')" />
            <x-text-input id="identity_number" class="block mt-1 w-full" type="text" name="identity_number" :value="old('identity_number')" required />
            <x-input-error :messages="$errors->get('identity_number')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
