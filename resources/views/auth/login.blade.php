<x-guest-layout>
    <style>
        .input-group { transition: all 0.3s ease; }
        .input-group:focus-within { transform: translateY(-2px); }
        .input-field { transition: all 0.3s ease; }
        .input-field:focus { transform: scale(1.02); box-shadow: 0 4px 20px rgba(11, 110, 162, 0.15); }
        .btn-submit { transition: all 0.3s ease; position: relative; overflow: hidden; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(11, 110, 162, 0.3); }
        .btn-submit:active { transform: translateY(0); }
        .link-hover { position: relative; }
        .link-hover::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: #0b6ea2; transition: width 0.3s ease; }
        .link-hover:hover::after { width: 100%; }
    </style>
    
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Welcome back</h1>
        <p class="text-slate-500">Sign in to continue to your ORDON dashboard.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <!-- Email Address -->
        <div class="input-group mb-5">
            <x-input-label for="email" :value="__('Email')" class="flex items-center gap-2 mb-2 text-slate-700 font-medium">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </x-input-label>
            <x-text-input id="email" class="input-field block mt-1 w-full px-4 py-3 rounded-xl border-2 @error('email') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @else border-slate-200 focus:border-cyan-500 focus:ring-cyan-100 @enderror focus:ring-4" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Password -->
        <div class="input-group mb-5">
            <x-input-label for="password" :value="__('Password')" class="flex items-center gap-2 mb-2 text-slate-700 font-medium">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v1h8z"></path>
                </svg>
            </x-input-label>
            <x-text-input id="password" class="input-field block mt-1 w-full px-4 py-3 rounded-xl border-2 @error('password') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @else border-slate-200 focus:border-cyan-500 focus:ring-cyan-100 @enderror focus:ring-4"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>

        <div class="flex items-center justify-end mb-6">
            @if (Route::has('password.request'))
                <a class="link-hover text-sm text-cyan-700 hover:text-cyan-900" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="btn-submit w-full py-3.5 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-xl justify-center disabled:cursor-not-allowed disabled:opacity-70" x-bind:disabled="submitting">
            <span class="flex items-center gap-2" x-show="!submitting">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                {{ __('Sign In') }}
            </span>
            <span x-show="submitting">Signing in...</span>
        </x-primary-button>

        <p class="text-center text-sm text-slate-500 mt-6">
            New user?
            <a href="{{ route('register') }}" class="text-cyan-700 font-semibold hover:text-cyan-900 link-hover">
                Create account
            </a>
        </p>
    </form>
</x-guest-layout>
