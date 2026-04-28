<x-guest-layout>
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Create ORDON Account</h1>
        <p class="text-slate-500">Join as a donor, recipient with RVID, or hospital partner.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ role: @js(old('role', '')), submitting: false }" @submit="submitting = true" class="space-y-5">
        @csrf

        <div>
            <x-input-label :value="__('Select Role')" class="mb-2 text-slate-700 font-medium" />
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="donor" class="sr-only" x-model="role">
                    <div class="rounded-xl border-2 p-4 text-center transition"
                        :class="role === 'donor' ? 'border-cyan-600 bg-cyan-50' : 'border-slate-200 hover:border-cyan-300 hover:bg-slate-50'">
                        <svg class="mx-auto mb-2 h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-slate-800">Donor</span>
                        <p class="mt-1 text-xs text-slate-500">Register your Donor account.</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="role" value="recipient" class="sr-only" x-model="role">
                    <div class="rounded-xl border-2 p-4 text-center transition"
                        :class="role === 'recipient' ? 'border-cyan-600 bg-cyan-50' : 'border-slate-200 hover:border-cyan-300 hover:bg-slate-50'">
                        <svg class="mx-auto mb-2 h-7 w-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-slate-800">Recipient</span>
                        <p class="mt-1 text-xs text-slate-500">Register with hospital RVID.</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="role" value="hospital" class="sr-only" x-model="role">
                    <div class="rounded-xl border-2 p-4 text-center transition"
                        :class="role === 'hospital' ? 'border-cyan-600 bg-cyan-50' : 'border-slate-200 hover:border-cyan-300 hover:bg-slate-50'">
                        <svg class="mx-auto mb-2 h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="text-sm font-semibold text-slate-800">Hospital</span>
                        <p class="mt-1 text-xs text-slate-500">Register your Hospital account</p>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-500 text-sm" />
        </div>

        <div x-show="role && role !== 'recipient'" x-transition>
            <x-input-label for="name" :value="__('Full Name')" class="mb-2 text-slate-700 font-medium" />
            <x-text-input id="name" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('name') border-rose-400 @enderror"
                type="text" name="name" :value="old('name')" autofocus autocomplete="name" placeholder="Enter your full name"
                x-bind:required="role && role !== 'recipient'" x-bind:disabled="role === 'recipient' || !role" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
        </div>

        <div x-show="role && role !== 'recipient'" x-transition>
            <x-input-label for="email" :value="__('Email Address')" class="mb-2 text-slate-700 font-medium" />
            <x-text-input id="email" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('email') border-rose-400 @enderror"
                type="email" name="email" :value="old('email')" autocomplete="username" placeholder="Enter your email"
                x-bind:required="role && role !== 'recipient'" x-bind:disabled="role === 'recipient' || !role" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
        </div>

        <div x-show="role === 'recipient'" x-transition class="space-y-5">
            <div>
                <x-input-label for="rvid" :value="__('Recipient Verification ID (RVID)')" class="mb-2 text-slate-700 font-medium" />
                <x-text-input id="rvid" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 uppercase focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('rvid') border-rose-400 @enderror"
                    type="text" name="rvid" :value="old('rvid')" placeholder="Enter hospital-generated RVID"
                    x-bind:required="role === 'recipient'" x-bind:disabled="role !== 'recipient'" @input="$el.value = $el.value.toUpperCase()" />
                <x-input-error :messages="$errors->get('rvid')" class="mt-2 text-red-500 text-sm" />
            </div>
        </div>

        <div x-show="role === 'donor' || role === 'recipient'" x-transition class="space-y-5">
            <div>
                <x-input-label for="identity_type" :value="__('Identity Type')" class="mb-2 text-slate-700 font-medium" />
                <select id="identity_type" name="identity_type"
                    class="block w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('identity_type') border-rose-400 @enderror"
                    :required="role === 'donor' || role === 'recipient'" :disabled="role !== 'donor' && role !== 'recipient'">
                    <option value="">Select identity type</option>
                    <option value="aadhaar" @selected(old('identity_type') === 'aadhaar')>Aadhaar</option>
                    <option value="passport" @selected(old('identity_type') === 'passport')>Passport</option>
                    <option value="voter_id" @selected(old('identity_type') === 'voter_id')>Voter ID</option>
                    <option value="driving_licence" @selected(old('identity_type') === 'driving_licence')>Driving Licence</option>
                    <option value="pan" @selected(old('identity_type') === 'pan')>PAN</option>
                    <option value="other" @selected(old('identity_type') === 'other')>Other</option>
                </select>
                <x-input-error :messages="$errors->get('identity_type')" class="mt-2 text-red-500 text-sm" />
            </div>

            <div>
                <x-input-label for="identity_number" :value="__('Identity Number')" class="mb-2 text-slate-700 font-medium" />
                <x-text-input id="identity_number" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('identity_number') border-rose-400 @enderror"
                    type="text" name="identity_number" :value="old('identity_number')" placeholder="Enter your identity number"
                    x-bind:required="role === 'donor' || role === 'recipient'" x-bind:disabled="role !== 'donor' && role !== 'recipient'" />
                <x-input-error :messages="$errors->get('identity_number')" class="mt-2 text-red-500 text-sm" />
            </div>
        </div>

        <div x-show="role === 'hospital'" x-transition class="space-y-5">
            <div>
                <x-input-label for="hospital_name" :value="__('Hospital Name')" class="mb-2 text-slate-700 font-medium" />
                <x-text-input id="hospital_name" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('hospital_name') border-rose-400 @enderror"
                    type="text" name="hospital_name" :value="old('hospital_name')" placeholder="Enter hospital name"
                    x-bind:required="role === 'hospital'" x-bind:disabled="role !== 'hospital'" />
                <x-input-error :messages="$errors->get('hospital_name')" class="mt-2 text-red-500 text-sm" />
            </div>

            <div>
                <x-input-label for="hospital_registration_id" :value="__('Hospital Registration ID')" class="mb-2 text-slate-700 font-medium" />
                <x-text-input id="hospital_registration_id" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 uppercase focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('hospital_registration_id') border-rose-400 @enderror"
                    type="text" name="hospital_registration_id" :value="old('hospital_registration_id')" placeholder="Enter hospital registration ID"
                    x-bind:required="role === 'hospital'" x-bind:disabled="role !== 'hospital'" @input="$el.value = $el.value.toUpperCase()" />
                <x-input-error :messages="$errors->get('hospital_registration_id')" class="mt-2 text-red-500 text-sm" />
            </div>

            <div>
                <x-input-label for="hospital_location" :value="__('Hospital Location')" class="mb-2 text-slate-700 font-medium" />
                <x-text-input id="hospital_location" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('hospital_location') border-rose-400 @enderror"
                    type="text" name="hospital_location" :value="old('hospital_location')" placeholder="City, State"
                    x-bind:required="role === 'hospital'" x-bind:disabled="role !== 'hospital'" />
                <x-input-error :messages="$errors->get('hospital_location')" class="mt-2 text-red-500 text-sm" />
            </div>
        </div>

        <div x-show="role" x-transition>
            <x-input-label for="password" :value="__('Password')" class="mb-2 text-slate-700 font-medium" />
            <x-text-input id="password" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 @error('password') border-rose-400 @enderror"
                type="password" name="password" autocomplete="new-password" placeholder="Create a strong password"
                x-bind:required="!!role" x-bind:disabled="!role" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>

        <div x-show="role" x-transition>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="mb-2 text-slate-700 font-medium" />
            <x-text-input id="password_confirmation" class="block w-full rounded-xl border-2 border-slate-200 px-4 py-3 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                type="password" name="password_confirmation" autocomplete="new-password" placeholder="Confirm your password"
                x-bind:required="!!role" x-bind:disabled="!role" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
        </div>

        <x-primary-button class="w-full justify-center rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 py-3.5 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-70" x-bind:disabled="submitting || !role">
            <span x-show="!submitting">Create Account</span>
            <span x-show="submitting">Creating Account...</span>
        </x-primary-button>

        <p class="text-center text-sm text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-cyan-700 hover:text-cyan-900">
                Sign in
            </a>
        </p>
    </form>
</x-guest-layout>
