<x-app-layout>
    <x-slot name="title">Recipient Dashboard</x-slot>

    @unless($accountApproved)
        <div class="mb-6 rounded-2xl bg-amber-100 text-amber-800 px-4 py-3">
            Registration complete. Your recipient account is pending admin ID verification and approval. Dashboard features are locked until approval.
        </div>
    @endunless

    <div class="card-pro mb-6" x-data="{ open: localStorage.getItem('recipient_journey_board') !== '0' }" x-init="$watch('open', v => localStorage.setItem('recipient_journey_board', v ? '1' : '0'))">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Recipient Care Journey Board</h3>
            <button type="button" @click="open = !open" class="text-xs rounded-lg border border-slate-300 px-2 py-1 text-slate-600 hover:bg-slate-100" x-text="open ? 'Collapse' : 'Expand'"></button>
        </div>
        <div x-show="open" x-transition class="grid md:grid-cols-4 gap-3 text-sm">
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="text-slate-500">Current Stage</p>
                <p class="font-semibold">{{ $recipient->status }}</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="text-slate-500">Compatibility Index</p>
                <p class="font-semibold">{{ $compatibilityIndex ? $compatibilityIndex.'%' : 'Pending' }}</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="text-slate-500">Approved Matches</p>
                <p class="font-semibold">{{ $approvedMatchesCount }}</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="text-slate-500">Emergency Requests</p>
                <p class="font-semibold">{{ $emergencyRequestCount }}</p>
            </div>
        </div>
    </div>

    <div class="card-pro mb-6">
        <h3 class="text-lg font-semibold mb-3">Identity Shield</h3>
        @php
            $maskedId = $recipient->identity_number ? str_repeat('*', max(strlen($recipient->identity_number) - 4, 0)).substr($recipient->identity_number, -4) : 'N/A';
        @endphp
        <div class="grid md:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">ID Type: <strong>{{ strtoupper($recipient->identity_type ?? 'N/A') }}</strong></div>
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">ID Number: <strong>{{ $maskedId }}</strong></div>
            <div class="rounded-xl border p-3 {{ $recipient->identity_verified ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                Verification: <strong>{{ $recipient->identity_verified ? 'Verified by Admin' : 'Pending Admin Verification' }}</strong>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="card-pro">
            <p class="text-slate-500">Urgency</p>
            <p class="text-3xl font-semibold mt-2 uppercase">{{ $recipient->urgency_level }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Waiting Time</p>
            <p class="text-3xl font-semibold mt-2">{{ $recipient->waiting_time }} days</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Match Status</p>
            <p class="text-3xl font-semibold mt-2">{{ $latestMatch->status ?? 'Pending' }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Queue Position</p>
            <p class="text-3xl font-semibold mt-2">{{ $queuePosition ?? 'N/A' }}<span class="text-base font-normal text-slate-500"> / {{ $queueSize }}</span></p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Hospital Approval</p>
            <p class="text-3xl font-semibold mt-2">{{ $recipient->hospital_verified ? 'Yes' : 'Pending' }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Admin Approval</p>
            <p class="text-3xl font-semibold mt-2">{{ $recipient->admin_approved ? 'Approved' : 'Pending' }}</p>
        </div>
    </div>

    <div class="card-pro mt-6" x-data="{ open: localStorage.getItem('recipient_advanced_insights') !== '0' }" x-init="$watch('open', v => localStorage.setItem('recipient_advanced_insights', v ? '1' : '0'))">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Advanced Recipient Insights</h3>
            <button type="button" @click="open = !open" class="text-xs rounded-lg border border-slate-300 px-2 py-1 text-slate-600 hover:bg-slate-100" x-text="open ? 'Collapse' : 'Expand'"></button>
        </div>
        <div x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="font-semibold text-slate-800">Clinical Priority Signal</p>
                <p class="text-slate-600 mt-1">Urgency, waiting duration, and emergency status are continuously reflected in your queue rank.</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="font-semibold text-slate-800">Approval Intelligence</p>
                <p class="text-slate-600 mt-1">Hospital verification and admin approval directly control when advanced transplant actions unlock.</p>
            </div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                <p class="font-semibold text-slate-800">Match Explainability</p>
                <p class="text-slate-600 mt-1">Recent match updates provide transparent score-based context for donor-recipient suitability.</p>
            </div>
        </div>
    </div>

    <div class="mt-6 card-pro">
        <h3 class="text-lg font-semibold mb-3">My Request (Hospital Managed)</h3>
        <p class="text-sm text-slate-600 mb-4">
            Recipient clinical/request details are maintained by your hospital team. You can monitor status, history, and match progress here.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3"><strong>Organ Needed:</strong> {{ $recipient->organ_needed }}</div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3"><strong>Blood Group:</strong> {{ $recipient->blood_group }}</div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3"><strong>Urgency:</strong> {{ strtoupper($recipient->urgency_level) }}</div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3"><strong>Waiting Time:</strong> {{ $recipient->waiting_time }} days</div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3"><strong>Region:</strong> {{ $recipient->region ?? 'N/A' }}</div>
            <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3"><strong>Identity Verified:</strong> {{ $recipient->identity_verified ? 'Yes' : 'Pending' }}</div>
        </div>
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
        <h3 class="text-lg font-semibold mb-4">Live Allocation Flow Model</h3>
        <p class="text-sm text-slate-600 mb-4">Real-time view of the organ allocation queue. Your position updates dynamically based on urgency, waiting time, and compatibility.</p>
        <div class="space-y-3">
            @foreach ($liveQueueSample as $index => $item)
                <div class="flex items-center gap-4 p-4 rounded-xl border {{ $item->id === $recipient->id ? 'border-blue-300 bg-blue-50' : 'border-slate-200 bg-white' }} shadow-sm">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold {{ match($item->urgency_level) { 'high' => 'bg-red-500', 'medium' => 'bg-yellow-500', default => 'bg-green-500' } }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-slate-800">{{ ucfirst($item->organ_needed) }} Transplant</span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ match($item->urgency_level) { 'high' => 'bg-red-100 text-red-800', 'medium' => 'bg-yellow-100 text-yellow-800', default => 'bg-green-100 text-green-800' } }}">
                                {{ ucfirst($item->urgency_level) }} Priority
                            </span>
                            @if($item->id === $recipient->id)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Your Position</span>
                            @endif
                        </div>
                        <div class="text-sm text-slate-600 mt-1">
                            Blood Group: {{ $item->blood_group }} | Waiting: {{ $item->waiting_time }} days
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            @endforeach
            @if($liveQueueSample->isEmpty())
                <div class="text-center py-8 text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <p class="text-lg font-medium">Queue is empty</p>
                    <p class="text-sm">No active recipients in the allocation queue.</p>
                </div>
            @endif
        </div>
        <div class="mt-4 text-center">
            <p class="text-sm text-slate-500">Showing top 5 positions. Full queue available in advanced views.</p>
        </div>
    </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recent Match Updates</h3>
        <div class="space-y-3">
            @forelse ($matchHistory as $match)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-slate-800">{{ $match->donor->user->name ?? 'Donor' }}</p>
                        <p class="text-xs text-slate-500">Score {{ $match->score }}</p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-slate-100">{{ $match->status }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No match history available yet.</p>
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
        <h3 class="text-lg font-semibold mb-4">My Recipient Feedback</h3>
        <form method="POST" action="{{ route('recipient.reports.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            @csrf
            <input type="hidden" name="scope" value="recipient">
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
