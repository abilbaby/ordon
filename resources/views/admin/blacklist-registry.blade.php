<x-app-layout>
    <x-slot name="title">Blacklist Registry</x-slot>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="card-pro">
            <h3 class="text-lg font-semibold mb-4">Blacklisted Donors</h3>
            <div class="space-y-2">
                @forelse ($donors as $donor)
                    <div class="rounded-xl border border-[#d7e8f4] p-3 text-sm">
                        <p class="font-medium">{{ $donor->user->name ?? 'Donor' }}</p>
                        <p class="text-slate-500">Blood: {{ $donor->blood_group }} | Organ: {{ $donor->organ_type }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No blacklisted donors.</p>
                @endforelse
            </div>
        </div>

        <div class="card-pro">
            <h3 class="text-lg font-semibold mb-4">Blacklisted Hospitals</h3>
            <div class="space-y-2">
                @forelse ($hospitals as $hospital)
                    <div class="rounded-xl border border-[#d7e8f4] p-3 text-sm">
                        <p class="font-medium">{{ $hospital->name }}</p>
                        <p class="text-slate-500">{{ $hospital->location }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No blacklisted hospitals.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
