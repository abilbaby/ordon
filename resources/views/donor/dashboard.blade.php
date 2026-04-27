<x-app-layout>
    <x-slot name="title">Donor Dashboard</x-slot>

    @unless($accountApproved)
        <div class="mb-6 rounded-2xl bg-amber-100 text-amber-800 px-4 py-3">
            Registration complete. Your donor account is pending admin ID verification and approval. Dashboard features are locked until approval.
        </div>
    @endunless

    <div class="card-pro mb-6" x-data="{ open: localStorage.getItem('donor_readiness_panel') !== '0' }" x-init="$watch('open', v => localStorage.setItem('donor_readiness_panel', v ? '1' : '0'))">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Donation Readiness Panel</h3>
            <button type="button" @click="open = !open" class="text-xs rounded-lg border border-slate-300 px-2 py-1 text-slate-600 hover:bg-slate-100" x-text="open ? 'Collapse' : 'Expand'"></button>
        </div>
        <div x-show="open" x-transition class="grid md:grid-cols-4 gap-3 text-sm">
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="text-slate-500">Profile</p>
                <p class="font-semibold">{{ $donor->medical_status }}</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="text-slate-500">Eligibility</p>
                <p class="font-semibold">{{ ucfirst($donor->eligibility_status) }}</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="text-slate-500">Availability</p>
                <p class="font-semibold">{{ $donor->is_available ? 'Active' : 'Inactive' }}</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="text-slate-500">Selected Organs</p>
                <p class="font-semibold">{{ count($selectedOrgans) }}</p>
            </div>
        </div>
    </div>

    <div class="card-pro mb-6">
        <h3 class="text-lg font-semibold mb-3">Identity Shield</h3>
        @php
            $maskedId = $donor->identity_number ? str_repeat('*', max(strlen($donor->identity_number) - 4, 0)).substr($donor->identity_number, -4) : 'N/A';
        @endphp
        <div class="grid md:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">ID Type: <strong>{{ strtoupper($donor->identity_type ?? 'N/A') }}</strong></div>
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">ID Number: <strong>{{ $maskedId }}</strong></div>
            <div class="rounded-xl border p-3 {{ $donor->identity_verified ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                Verification: <strong>{{ $donor->identity_verified ? 'Verified by Admin' : 'Pending Admin Verification' }}</strong>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="card-pro">
            <h3 class="text-lg font-semibold mb-4">Donor Profile</h3>
            <form method="POST" action="{{ route('donor.profile.update') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Blood Group</label>
                    <select name="blood_group" class="w-full rounded-xl border-slate-200">
                        @foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $group)
                            <option value="{{ $group }}" @selected($donor->blood_group === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Donation Type</label>
                    <select name="donation_type" class="w-full rounded-xl border-slate-200">
                        @foreach ($donationTypes as $type)
                            <option value="{{ $type }}" @selected($donor->donation_type === $type)>
                                {{ $type === 'LivingDonation' ? 'Donate Now (Living)' : 'Donate After Death' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Region</label>
                    <input name="region" value="{{ $donor->region }}" class="w-full rounded-xl border-slate-200" />
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Organs (Multi-select)</label>
                    <div class="rounded-xl border border-slate-200 p-3 grid grid-cols-2 gap-2">
                        @foreach ($organOptions as $organ)
                            <label class="text-sm flex items-center gap-2">
                                <input type="checkbox" name="organs[]" value="{{ $organ }}" @checked(in_array($organ, $selectedOrgans, true))>
                                <span>{{ $organ }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-slate-600 mb-1">Medical Conditions</label>
                    <textarea name="medical_conditions" class="w-full rounded-xl border-slate-200">{{ $donor->medical_conditions }}</textarea>
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Family Contact</label>
                    <input name="family_contact" value="{{ $donor->family_contact }}" class="w-full rounded-xl border-slate-200" />
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Donation Notes (Optional)</label>
                    <input name="notes" value="{{ $donor->notes }}" class="w-full rounded-xl border-slate-200" />
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Available Until</label>
                    <input type="datetime-local" name="available_until" value="{{ $donor->available_until?->format('Y-m-d\TH:i') }}" class="w-full rounded-xl border-slate-200" />
                </div>
                <button class="rounded-2xl bg-slate-900 text-white px-5 py-2.5" @disabled(! $accountApproved)>Update Profile</button>
            </form>
            <p class="mt-4 text-slate-700"><strong>Medical Status:</strong> {{ $donor->medical_status }}</p>
            <p class="mt-1 text-slate-700"><strong>Eligibility:</strong> {{ ucfirst($donor->eligibility_status) }}</p>
            <p class="mt-1 text-slate-700"><strong>Identity Verified:</strong> {{ $donor->identity_verified ? 'Yes' : 'Pending' }}</p>
            <p class="mt-1 text-slate-700"><strong>Selected Organs:</strong> {{ count($selectedOrgans) ? implode(', ', $selectedOrgans) : 'Not selected yet' }}</p>
        </div>

        <div class="card-pro space-y-4" x-data="{ open: localStorage.getItem('donor_action_center') !== '0' }" x-init="$watch('open', v => localStorage.setItem('donor_action_center', v ? '1' : '0'))">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Action Center</h3>
                <button type="button" @click="open = !open" class="text-xs rounded-lg border border-slate-300 px-2 py-1 text-slate-600 hover:bg-slate-100" x-text="open ? 'Collapse' : 'Expand'"></button>
            </div>
            <div x-show="open" x-transition class="space-y-4">
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-2">Step 1: Keep profile updated</div>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-2">Step 2: Enable availability</div>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-2">Step 3: Run matching</div>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-2">Step 4: Handle urgent requests</div>
            </div>
            <form method="POST" action="{{ route('donor.availability.toggle') }}">
                @csrf
                <button class="w-full rounded-2xl bg-slate-900 text-white p-3 transition-all duration-200 hover:bg-slate-700" @disabled(! $accountApproved)>
                    Toggle Availability (Current: {{ $donor->is_available ? 'ON' : 'OFF' }})
                </button>
            </form>
            <form method="POST" action="{{ route('donor.match') }}">
                @csrf
                <button class="w-full rounded-2xl bg-emerald-600 text-white p-3 transition-all duration-200 hover:bg-emerald-500" @disabled(! $accountApproved)>
                    Find Best Recipient Match
                </button>
            </form>
            <form method="POST" action="{{ route('donor.consent.update') }}" class="grid grid-cols-2 gap-2">
                @csrf
                <label class="text-sm"><input type="hidden" name="consent_given" value="0"><input type="checkbox" name="consent_given" value="1" @checked($donor->consent_given)> Consent</label>
                <label class="text-sm"><input type="hidden" name="pre_donation_checklist_completed" value="0"><input type="checkbox" name="pre_donation_checklist_completed" value="1" @checked($donor->pre_donation_checklist_completed)> Checklist</label>
                <button class="col-span-2 rounded-2xl bg-indigo-600 text-white p-2.5" @disabled(! $accountApproved)>Save Consent and Safety Checklist</button>
            </form>
            </div>
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Notification Preview</h3>
        <div class="space-y-2">
            @forelse ($notificationPreview as $notification)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between text-sm">
                    <span>{{ $notification->title }}</span>
                    <span class="text-slate-500">{{ $notification->created_at?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No notifications yet.</p>
            @endforelse
        </div>
        <a href="{{ route('notifications.index') }}" class="inline-block mt-3 text-sm text-[#0b6ea2]">Open full notification center</a>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Status Timeline</h3>
        <div class="space-y-2 mb-4">
            @forelse ($statusHistories as $entry)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex justify-between text-sm">
                    <span>{{ $entry->old_status ?? 'N/A' }} -> <strong>{{ $entry->new_status }}</strong></span>
                    <span class="text-slate-500">{{ $entry->changed_at?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No timeline events yet.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">My Donation History</h3>
        <div class="space-y-2">
            @forelse ($donationHistory as $item)
                <div class="rounded-xl border border-[#d7e8f4] p-3 text-sm flex justify-between">
                    <div>
                        <p class="font-medium">{{ $item->organ_type }} donation</p>
                        <p class="text-slate-500">Recipient: {{ $item->recipient->user->name ?? 'Recipient' }} | Hospital: {{ $item->hospital->name ?? 'Hospital' }}</p>
                    </div>
                    <div class="text-right">
                        <p>{{ $item->status }}</p>
                        <p class="text-xs text-slate-500">{{ $item->donation_date?->format('Y-m-d') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No donation records yet.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Emergency Donation Requests</h3>
        <div class="space-y-3">
            @forelse ($emergencyRequests as $requestItem)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-700">{{ $requestItem->organ_type }} / {{ $requestItem->blood_group }}</p>
                        <p class="text-xs text-slate-500">Requested {{ $requestItem->created_at?->diffForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('donor.emergency.accept', $requestItem) }}">
                        @csrf
                        <button class="rounded-xl bg-rose-600 text-white px-3 py-1.5 text-xs" @disabled(! $accountApproved)>Accept</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-500">No emergency requests matching your profile.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recent Match Timeline</h3>
        <div class="space-y-3">
            @forelse ($recentMatches as $match)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-slate-800">{{ $match->recipient->user->name ?? 'Recipient' }}</p>
                        <p class="text-xs text-slate-500">Score: {{ $match->score }}</p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-slate-100">{{ $match->status }}</span>
                </div>
            @empty
                <p class="text-slate-500 text-sm">No matches yet. Run allocation engine to find one.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Activity Notifications</h3>
        <div class="space-y-3">
            @forelse ($activities as $activity)
                <div class="rounded-xl border border-[#d7e8f4] px-3 py-2 flex items-center justify-between">
                    <p class="text-sm text-slate-700"><span class="status-dot status-dot-{{ $activity['type'] ?? 'info' }}"></span>{{ $activity['label'] }}</p>
                    <span class="text-xs text-slate-500">{{ $activity['time']?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No activity yet.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">My Donor Feedback</h3>
        <form method="POST" action="{{ route('donor.reports.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            @csrf
            <input type="hidden" name="scope" value="donor">
            <input name="subject" class="rounded-xl border-[#c8dfef]" placeholder="Feedback subject" required>
            <input name="message" class="rounded-xl border-[#c8dfef] md:col-span-2" placeholder="Describe your issue..." required>
            <button class="md:col-span-3 rounded-xl bg-slate-900 text-white px-4 py-2.5" @disabled(! $accountApproved)>Submit Feedback</button>
        </form>
        @foreach ($reports as $report)
            <div class="rounded-xl border border-[#d7e8f4] p-3 text-sm mb-2">
                {{ $report->subject }} - <span class="uppercase">{{ $report->status }}</span>
            </div>
        @endforeach
    </div>

</x-app-layout>
