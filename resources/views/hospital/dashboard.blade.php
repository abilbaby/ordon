<x-app-layout>
    <x-slot name="title">Hospital Dashboard</x-slot>

    @unless($accountApproved)
        <div class="mb-6 rounded-2xl bg-amber-100 text-amber-800 px-4 py-3">
            Registration complete. Your hospital account is pending admin ID verification and approval. Dashboard features are locked until approval.
        </div>
    @endunless

    <div class="card-pro mb-6" x-data="{ open: localStorage.getItem('hospital_ops_radar') !== '0' }" x-init="$watch('open', v => localStorage.setItem('hospital_ops_radar', v ? '1' : '0'))">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">Hospital Operations Radar</h3>
            <button type="button" @click="open = !open" class="text-xs rounded-lg border border-slate-300 px-2 py-1 text-slate-600 hover:bg-slate-100" x-text="open ? 'Collapse' : 'Expand'"></button>
        </div>
        <div x-show="open" x-transition class="grid md:grid-cols-4 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">
                <p class="text-slate-500">Invitations Sent</p>
                <p class="font-semibold">{{ $recipientInvites->count() }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">
                <p class="text-slate-500">Pending Clinical Reviews</p>
                <p class="font-semibold">{{ $pendingMatches->count() }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">
                <p class="text-slate-500">Recipient Approvals Pending</p>
                <p class="font-semibold">{{ $pendingRecipientApprovals->count() }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3">
                <p class="text-emerald-700">Completed Outcomes</p>
                <p class="font-semibold text-emerald-800">{{ $stats['completed'] }}</p>
            </div>
        </div>
    </div>

    <div class="card-pro mb-6">
        <h3 class="text-lg font-semibold mb-3">Identity Shield</h3>
        @php
            $maskedId = $hospital->identity_number ? str_repeat('*', max(strlen($hospital->identity_number) - 4, 0)).substr($hospital->identity_number, -4) : 'N/A';
        @endphp
        <div class="grid md:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">ID Type: <strong>{{ strtoupper($hospital->identity_type ?? 'N/A') }}</strong></div>
            <div class="rounded-xl bg-slate-50 border border-[#d7e8f4] p-3">ID Number: <strong>{{ $maskedId }}</strong></div>
            <div class="rounded-xl border p-3 {{ $hospital->identity_verified ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                Verification: <strong>{{ $hospital->identity_verified ? 'Verified by Admin' : 'Pending Admin Verification' }}</strong>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card-pro">
            <p class="text-slate-500">Pending Approvals</p>
            <p class="text-3xl font-semibold mt-2">{{ $stats['pending'] }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Approved Transplants</p>
            <p class="text-3xl font-semibold mt-2">{{ $stats['approved'] }}</p>
        </div>
        <div class="card-pro">
            <p class="text-slate-500">Completed Transplants</p>
            <p class="text-3xl font-semibold mt-2">{{ $stats['completed'] }}</p>
        </div>
    </div>

    <div class="card-pro mb-6">
        <h3 class="text-lg font-semibold">{{ $hospital->name }}</h3>
        <p class="text-slate-600">{{ $hospital->location }}</p>
        <p class="text-xs text-slate-500 mt-2">Identity updates are managed through admin verification flow.</p>
    </div>

    <div class="card-pro mb-6">
        <h3 class="text-lg font-semibold mb-3">Create Recipient Invitation</h3>
        <form method="POST" action="{{ route('hospital.recipient-invites.create') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @csrf
            <input name="recipient_name" class="rounded-xl border-slate-200" placeholder="Recipient Name" required>
            <input type="email" name="email" class="rounded-xl border-slate-200" placeholder="Email" required>
            <input name="phone" class="rounded-xl border-slate-200" placeholder="Phone" required>
            <select name="blood_group" class="rounded-xl border-slate-200" required>
                <option value="">Blood Group</option>
                @foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $group)
                    <option value="{{ $group }}">{{ $group }}</option>
                @endforeach
            </select>
            <textarea name="notes" class="rounded-xl border-slate-200 md:col-span-2" placeholder="Optional notes"></textarea>
            <button class="md:col-span-2 rounded-xl bg-slate-900 text-white px-4 py-2.5">Generate Link + Send Email</button>
        </form>
    </div>

    <div class="card-pro">
        <h3 class="text-lg font-semibold mb-4">Approval Queue</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="p-3">Donor</th>
                        <th class="p-3">Recipient</th>
                        <th class="p-3">Score</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingMatches as $match)
                        <tr class="hover:bg-slate-50 transition-all duration-200" x-data="{ edit: false }">
                            <td class="p-3">{{ $match->donor->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->recipient->user->name ?? 'N/A' }}</td>
                            <td class="p-3">{{ $match->score }}</td>
                            <td class="p-3">
                                <div x-show="!edit" class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] bg-slate-100 text-slate-700">{{ $match->status }}</span>
                                    <button type="button" @click="edit = true" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Edit</button>
                                </div>
                                <div x-show="edit" x-transition class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('hospital.matches.validate', $match) }}">
                                        @csrf
                                        <button class="rounded-xl bg-cyan-700 text-white px-4 py-2">Validate</button>
                                    </form>
                                    <form method="POST" action="{{ route('hospital.matches.approve', $match) }}">
                                        @csrf
                                        <button class="rounded-xl bg-emerald-600 text-white px-4 py-2">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('hospital.matches.reject', $match) }}">
                                        @csrf
                                        <button class="rounded-xl bg-rose-600 text-white px-4 py-2">Reject</button>
                                    </form>
                                    <form method="POST" action="{{ route('hospital.matches.complete', $match) }}">
                                        @csrf
                                        <button class="rounded-xl bg-slate-900 text-white px-4 py-2">Complete</button>
                                    </form>
                                    <button type="button" @click="edit = false" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Done</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-3" colspan="4">No pending approvals.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <div class="card-pro">
            <h3 class="text-lg font-semibold mb-3">Doctor Management</h3>
            <form method="POST" action="{{ route('hospital.doctors.add') }}" class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
                @csrf
                <input name="name" class="rounded-xl border-slate-200" placeholder="Doctor name" required>
                <input name="specialization" class="rounded-xl border-slate-200" placeholder="Specialization" required>
                <input name="phone" class="rounded-xl border-slate-200" placeholder="Phone">
                <button class="md:col-span-3 rounded-xl bg-slate-900 text-white py-2">Add Doctor</button>
            </form>
            @foreach ($doctors as $doctor)
                <div class="rounded-xl border border-[#d7e8f4] p-2 text-sm mb-2">{{ $doctor->name }} - {{ $doctor->specialization }}</div>
            @endforeach
        </div>
        <div class="card-pro">
            <h3 class="text-lg font-semibold mb-3">Organ Inventory</h3>
            <form method="POST" action="{{ route('hospital.inventory.add') }}" class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                @csrf
                <select name="organ_type" class="rounded-xl border-slate-200" required>
                    <option value="">Select Organ</option>
                    @foreach (['Kidney','Liver','Heart','Lung','Pancreas','Intestine','Cornea'] as $organ)
                        <option value="{{ $organ }}">{{ $organ }}</option>
                    @endforeach
                </select>
                <input type="number" min="0" name="units" class="rounded-xl border-slate-200" placeholder="Units" required>
                <button class="md:col-span-2 rounded-xl bg-[#0b6ea2] text-white py-2">Update Inventory</button>
            </form>
            @foreach ($inventory as $item)
                <div class="rounded-xl border border-[#d7e8f4] p-2 text-sm mb-2">{{ $item->organ_type }}: {{ $item->units }} units</div>
            @endforeach
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Status Timeline</h3>
        <div class="space-y-2">
            @forelse ($statusHistories as $entry)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between text-sm">
                    <span>{{ class_basename($entry->entity_type) }}: {{ $entry->old_status ?? 'N/A' }} -> <strong>{{ $entry->new_status }}</strong></span>
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
        <h3 class="text-lg font-semibold mb-4">Recorded Donations</h3>
        <div class="space-y-2">
            @forelse ($donationHistory as $item)
                <div class="rounded-xl border border-[#d7e8f4] p-3 text-sm flex justify-between">
                    <div>
                        <p class="font-medium">{{ $item->organ_type }} - {{ $item->status }}</p>
                        <p class="text-slate-500">Donor: {{ $item->donor->user->name ?? 'Donor' }} | Recipient: {{ $item->recipient->user->name ?? 'Recipient' }}</p>
                    </div>
                    <span class="text-xs text-slate-500">{{ $item->donation_date?->format('Y-m-d') }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No donation records yet.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recipient Doctor Approval Workflow</h3>
        <div class="space-y-2">
            @forelse ($pendingRecipientApprovals as $recipient)
                @php
                    $recipientApprovalStatus = $recipient->status === 'REJECTED'
                        ? 'Rejected'
                        : ($recipient->hospital_verified ? 'Approved' : 'Pending');
                    $recipientApprovalClass = $recipientApprovalStatus === 'Approved'
                        ? 'bg-emerald-100 text-emerald-700'
                        : ($recipientApprovalStatus === 'Rejected' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700');
                @endphp
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between" x-data="{ edit: false }">
                    <p class="text-sm">{{ $recipient->user->name ?? 'Recipient' }} / {{ $recipient->blood_group }} / ID: {{ $recipient->masked_identity }}</p>
                    <div x-show="!edit" class="flex items-center gap-2">
                        <span class="rounded-full px-2.5 py-1 text-[11px] {{ $recipientApprovalClass }}">{{ $recipientApprovalStatus }}</span>
                        <button type="button" @click="edit = true" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Edit</button>
                    </div>
                    <div x-show="edit" x-transition class="flex gap-2">
                        <form method="POST" action="{{ route('hospital.recipient.approve', $recipient) }}">
                            @csrf
                            <button class="rounded-xl bg-emerald-600 text-white px-3 py-1.5 text-xs">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('hospital.recipient.reject', $recipient) }}">
                            @csrf
                            <button class="rounded-xl bg-rose-600 text-white px-3 py-1.5 text-xs">Reject</button>
                        </form>
                        <button type="button" @click="edit = false" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Done</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No pending recipient approvals.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recipient Invitation Links</h3>
        <div class="space-y-2">
            @forelse ($recipientInvites as $invite)
                <div class="rounded-xl border border-[#d7e8f4] p-3 text-sm">
                    <p class="font-medium">{{ $invite->recipient_name }} ({{ $invite->email }})</p>
                    <p class="text-slate-500">RVID: {{ $invite->rvid }} | Status: {{ $invite->status }} | Expires: {{ $invite->expires_at?->format('Y-m-d H:i') }}</p>
                    <a href="{{ $invite->registration_link }}" class="text-[#0b6ea2] text-xs break-all">{{ $invite->registration_link }}</a>
                </div>
            @empty
                <p class="text-sm text-slate-500">No invitation links generated yet.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Recipient Profile Change Requests</h3>
        <div class="space-y-2">
            @forelse ($recipientChangeRequests as $changeRequest)
                <div class="rounded-xl border border-[#d7e8f4] p-3 flex items-center justify-between" x-data="{ edit: false }">
                    <div class="text-sm">
                        <p class="font-medium">{{ $changeRequest->recipient->user->name ?? 'Recipient' }} requested correction</p>
                        <p class="text-slate-500">
                            Organ: {{ $changeRequest->payload['organ_needed'] ?? 'N/A' }},
                            Urgency: {{ strtoupper($changeRequest->payload['urgency_level'] ?? 'N/A') }},
                            Waiting: {{ $changeRequest->payload['waiting_time'] ?? 'N/A' }} days
                        </p>
                    </div>
                    <div x-show="!edit" class="flex items-center gap-2">
                        <span class="rounded-full px-2.5 py-1 text-[11px] bg-amber-100 text-amber-700">PENDING</span>
                        <button type="button" @click="edit = true" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Edit</button>
                    </div>
                    <div x-show="edit" x-transition class="flex gap-2">
                        <form method="POST" action="{{ route('hospital.recipient-change.approve', $changeRequest) }}">
                            @csrf
                            <button class="rounded-xl bg-emerald-600 text-white px-3 py-1.5 text-xs">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('hospital.recipient-change.reject', $changeRequest) }}">
                            @csrf
                            <button class="rounded-xl bg-rose-600 text-white px-3 py-1.5 text-xs">Reject</button>
                        </form>
                        <button type="button" @click="edit = false" class="rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">Done</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No recipient profile change requests pending.</p>
            @endforelse
        </div>
    </div>

    <div class="card-pro mt-6">
        <h3 class="text-lg font-semibold mb-4">Site Reports (Hospital)</h3>
        <form method="POST" action="{{ route('hospital.reports.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            @csrf
            <input type="hidden" name="scope" value="site">
            <input name="subject" class="rounded-xl border-[#c8dfef]" placeholder="Site report subject" required>
            <input name="message" class="rounded-xl border-[#c8dfef] md:col-span-2" placeholder="Describe system/report issue..." required>
            <button class="md:col-span-3 rounded-xl bg-slate-900 text-white px-4 py-2.5">Submit Site Report</button>
        </form>
        @foreach ($reports as $report)
            <div class="rounded-xl border border-[#d7e8f4] p-3 text-sm mb-2">
                {{ $report->subject }} - <span class="uppercase">{{ $report->status }}</span>
            </div>
        @endforeach
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

</x-app-layout>
