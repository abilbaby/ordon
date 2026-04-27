<x-guest-layout>
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <h2 class="text-xl font-semibold text-amber-900">Invitation Link Unavailable</h2>
        <p class="text-sm text-amber-800 mt-2">
            {{ $message ?? 'This recipient invitation link is invalid, expired, or already used.' }}
        </p>
        <p class="text-sm text-slate-600 mt-4">
            Please contact your hospital to request a new registration invitation link.
        </p>
    </div>
    <div class="mt-5">
        <a href="{{ route('contact') }}" class="inline-block rounded-xl bg-slate-900 text-white px-4 py-2.5 text-sm">
            Contact Support
        </a>
    </div>
</x-guest-layout>
