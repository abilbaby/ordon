<x-app-layout>
    <x-slot name="title">Recipient Dashboard</x-slot>

    @php
        $status = strtoupper((string) $recipient->status);
        $matchStatus = $latestMatch?->status ? strtoupper((string) $latestMatch->status) : 'PENDING';
        $currentStatus = $status === 'REJECTED'
            ? 'Rejected'
            : ($recipient->admin_approved ? 'Approved' : ($recipient->identity_verified || $recipient->hospital_verified ? 'Verified' : 'Pending'));
        $queueLabel = $queuePosition ? $queuePosition.'/'.$queueSize : 'N/A';
        $maskedId = $recipient->masked_identity;
        $identityStatus = $recipient->identity_verified ? 'Verified' : 'Pending';
        $timelineStages = ['Registered', 'Verified', 'Matched', 'Approved', 'Completed'];
        $timelineCompleted = [
            'Registered' => true,
            'Verified' => $recipient->identity_verified || $recipient->hospital_verified || $recipient->admin_approved || in_array($status, ['VERIFIED', 'MATCHED', 'APPROVED', 'COMPLETED'], true),
            'Matched' => (bool) $latestMatch || in_array($status, ['MATCHED', 'APPROVED', 'COMPLETED'], true),
            'Approved' => $recipient->admin_approved || in_array($matchStatus, ['APPROVED', 'COMPLETED'], true),
            'Completed' => $status === 'COMPLETED' || $matchStatus === 'COMPLETED',
        ];
        $badgeClass = fn (?string $value) => match (strtolower((string) $value)) {
            'approved', 'verified', 'completed', 'matched' => 'badge-approved',
            'rejected', 'failed' => 'badge-rejected',
            default => 'badge-pending',
        };
    @endphp

    @unless($accountApproved)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Waiting for approval. Your registration is complete, and hospital/admin verification is still in progress.
        </div>
    @endunless

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card-pro">
            <p class="text-sm font-medium text-slate-500">Current Status</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-bold text-slate-950">{{ $currentStatus }}</p>
                <span class="{{ $badgeClass($currentStatus) }}">{{ $currentStatus }}</span>
            </div>
        </div>
        <div class="card-pro">
            <p class="text-sm font-medium text-slate-500">Match Status</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-bold text-slate-950">{{ ucfirst(strtolower($matchStatus)) }}</p>
                <span class="{{ $badgeClass($matchStatus) }}">{{ ucfirst(strtolower($matchStatus)) }}</span>
            </div>
        </div>
        <div class="card-pro">
            <p class="text-sm font-medium text-slate-500">Queue Position</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-bold text-slate-950">{{ $queueLabel }}</p>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Active queue</span>
            </div>
        </div>
    </section>

    <section class="card-pro mt-6">
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-950">Progress Timeline</h3>
                <p class="text-sm text-slate-500">Your care workflow from registration to transplant completion.</p>
            </div>
            <span class="{{ $badgeClass($currentStatus) }}">{{ $currentStatus }}</span>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-5">
            @foreach ($timelineStages as $stage)
                <div class="rounded-xl border {{ $timelineCompleted[$stage] ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50' }} p-4">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full text-sm font-bold {{ $timelineCompleted[$stage] ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                            {{ $timelineCompleted[$stage] ? '✓' : $loop->iteration }}
                        </span>
                    </div>
                    <p class="font-semibold {{ $timelineCompleted[$stage] ? 'text-emerald-800' : 'text-slate-500' }}">{{ $stage }}</p>
                    <p class="mt-1 text-xs {{ $timelineCompleted[$stage] ? 'text-emerald-700' : 'text-slate-400' }}">{{ $timelineCompleted[$stage] ? 'Completed' : 'Pending' }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <div class="card-pro">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="font-semibold text-slate-900">{{ strtoupper($recipient->identity_type ?? 'ID') }}</span>
                        <span class="text-slate-300">|</span>
                        <span class="font-mono text-slate-700">{{ $maskedId }}</span>
                        <span class="text-slate-300">|</span>
                        <span class="{{ $recipient->identity_verified ? 'badge-approved' : 'badge-pending' }}">{{ $identityStatus }}</span>
                    </div>
                    <p class="text-xs text-slate-500">Identity Shield</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="card-pro p-4">
                    <p class="text-sm text-slate-500">Urgency</p>
                    <p class="mt-2 text-xl font-bold uppercase text-slate-950">{{ $recipient->urgency_level }}</p>
                </div>
                <div class="card-pro p-4">
                    <p class="text-sm text-slate-500">Waiting Time</p>
                    <p class="mt-2 text-xl font-bold text-slate-950">{{ $recipient->waiting_time }} days</p>
                </div>
                <div class="card-pro p-4">
                    <p class="text-sm text-slate-500">Approved Matches</p>
                    <p class="mt-2 text-xl font-bold text-slate-950">{{ $approvedMatchesCount }}</p>
                </div>
                <div class="card-pro p-4">
                    <p class="text-sm text-slate-500">Emergency Requests</p>
                    <p class="mt-2 text-xl font-bold text-slate-950">{{ $emergencyRequestCount }}</p>
                </div>
            </div>

            <div class="card-pro">
                <h3 class="text-lg font-semibold text-slate-950">My Request</h3>
                <div class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Organ Needed</span><p class="font-semibold text-slate-900">{{ $recipient->organ_needed }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Blood Group</span><p class="font-semibold text-slate-900">{{ $recipient->blood_group }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Region</span><p class="font-semibold text-slate-900">{{ $recipient->region ?? 'N/A' }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Hospital Approval</span><p class="font-semibold text-slate-900">{{ $recipient->hospital_verified ? 'Verified' : 'Waiting for approval' }}</p></div>
                </div>
            </div>

            <div class="card-pro">
                <h3 class="text-lg font-semibold text-slate-950">Recent Match Updates</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($matchHistory as $match)
                        <div class="flex flex-col gap-2 rounded-xl border border-slate-200 p-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-medium text-slate-900">{{ $match->donor->user->name ?? 'Donor' }}</p>
                                <p class="text-xs text-slate-500">Score {{ $match->score }}</p>
                            </div>
                            <span class="{{ $badgeClass($match->status) }}">{{ ucfirst(strtolower($match->status)) }}</span>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                            No matches yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="card-pro">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-950">Notifications</h3>
                    <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-cyan-700 hover:text-cyan-900">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse ($notificationPreview->take(5) as $notification)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <p class="text-sm font-medium text-slate-900">{{ $notification->title }}</p>
                            @if($notification->message)
                                <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($notification->message, 110) }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at?->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                            No notifications yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card-pro">
                <h3 class="text-lg font-semibold text-slate-950">Activity</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($activities as $activity)
                        <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-200 p-3">
                            <p class="text-sm text-slate-700"><span class="status-dot status-dot-{{ $activity['type'] ?? 'info' }}"></span>{{ $activity['label'] }}</p>
                            <span class="shrink-0 text-xs text-slate-500">{{ $activity['time']?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                            No activity yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>

    <section class="card-pro mt-6">
        <h3 class="text-lg font-semibold text-slate-950">My Recipient Feedback</h3>
        <form method="POST" action="{{ route('recipient.reports.store') }}" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <input type="hidden" name="scope" value="recipient">
            <div>
                <input name="subject" class="form-control @error('subject') form-control-invalid @enderror" placeholder="Feedback subject" required>
                @error('subject') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <input name="message" class="form-control @error('message') form-control-invalid @enderror" placeholder="Describe your issue..." required>
                @error('message') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60 md:col-span-3" @disabled(! $accountApproved) :disabled="submitting || {{ $accountApproved ? 'false' : 'true' }}">
                <span x-show="!submitting">Submit Feedback</span>
                <span x-show="submitting">Submitting...</span>
            </button>
        </form>
        <div class="mt-4 space-y-2">
            @forelse ($reports as $report)
                <div class="rounded-xl border border-slate-200 p-3 text-sm">
                    {{ $report->subject }} - <span class="uppercase">{{ $report->status }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No feedback submitted yet.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
