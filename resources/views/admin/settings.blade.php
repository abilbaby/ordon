<x-app-layout>
    <x-slot name="title">System Settings</x-slot>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Allocation and Operations Settings</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm text-slate-600 mb-1">Urgency Weight</label>
                <input type="number" name="urgency_weight" value="{{ $settings->urgency_weight }}" class="w-full rounded-xl border-slate-200" min="1" max="100">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1">Waiting Time Weight</label>
                <input type="number" name="waiting_weight" value="{{ $settings->waiting_weight }}" class="w-full rounded-xl border-slate-200" min="1" max="100">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1">Compatibility Weight</label>
                <input type="number" name="compatibility_weight" value="{{ $settings->compatibility_weight }}" class="w-full rounded-xl border-slate-200" min="1" max="100">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1">Emergency Threshold (days)</label>
                <input type="number" name="emergency_threshold" value="{{ $settings->emergency_threshold }}" class="w-full rounded-xl border-slate-200" min="1" max="3650">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1">Max Daily Surgeries</label>
                <input type="number" name="max_daily_surgeries" value="{{ $settings->max_daily_surgeries }}" class="w-full rounded-xl border-slate-200" min="1" max="100">
            </div>
            <div class="md:col-span-2">
                <button class="rounded-xl bg-slate-900 text-white px-5 py-2.5">Save Settings</button>
            </div>
        </form>
    </div>
</x-app-layout>
