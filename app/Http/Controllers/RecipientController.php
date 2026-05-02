<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRecipientProfileRequest;
use App\Models\AllocationMatch;
use App\Models\EmergencyRequest;
use App\Models\IssueReport;
use App\Models\Recipient;
use App\Models\RecipientChangeRequest;
use App\Models\StatusHistory;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class RecipientController extends Controller
{
    public function dashboard(Request $request): View
    {
        $recipient = Recipient::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'blood_group' => 'A+',
                'organ_needed' => 'kidney',
                'urgency_level' => 'medium',
                'waiting_time' => 12,
                'status' => 'REGISTERED',
            ]
        );

        $latestMatch = AllocationMatch::where('recipient_id', $recipient->id)->latest()->first();
        $matchHistory = AllocationMatch::with('donor.user')
            ->where('recipient_id', $recipient->id)
            ->latest()
            ->limit(5)
            ->get();
        $queue = Recipient::query()
            ->where('organ_needed', $recipient->organ_needed)
            ->whereIn('status', ['REGISTERED', 'VERIFIED', 'MATCHED'])
            ->get()
            ->sortByDesc(function (Recipient $entry): int {
                $urgencyWeight = match ($entry->urgency_level) {
                    'high' => 3,
                    'medium' => 2,
                    default => 1,
                };

                return ($urgencyWeight * 10000) + $entry->waiting_time;
            })
            ->values();
        $queuePosition = $queue->search(fn (Recipient $entry): bool => $entry->id === $recipient->id);

        return view('recipient.dashboard', [
            'recipient' => $recipient,
            'accountApproved' => $this->isRecipientApproved($recipient),
            'latestMatch' => $latestMatch,
            'matchHistory' => $matchHistory,
            'queuePosition' => $queuePosition !== false ? $queuePosition + 1 : null,
            'queueSize' => $queue->count(),
            'approvedMatchesCount' => AllocationMatch::where('recipient_id', $recipient->id)
                ->where('status', 'APPROVED')
                ->count(),
            'compatibilityIndex' => $latestMatch ? min(100, max(1, (int) round(($latestMatch->score / 180) * 100))) : null,
            'emergencyRequestCount' => EmergencyRequest::where('recipient_id', $recipient->id)->count(),
            'activities' => $matchHistory->map(fn (AllocationMatch $match): array => [
                'label' => "Donor ".($match->donor->user->name ?? 'Unknown')." - {$match->status}",
                'time' => $match->created_at,
                'type' => $match->status === 'APPROVED' ? 'success' : ($match->status === 'REJECTED' ? 'danger' : 'warning'),
            ]),
            'liveQueueSample' => $queue->take(5),
            'reports' => IssueReport::where('user_id', $request->user()->id)->latest()->limit(5)->get(),
            'statusHistories' => StatusHistory::with('user')
                ->where('entity_type', Recipient::class)
                ->where('entity_id', $recipient->id)
                ->latest('changed_at')
                ->limit(10)
                ->get(),
            'notificationPreview' => UserNotification::where('user_id', $request->user()->id)->latest()->limit(5)->get(),
        ]);
    }

    public function requests(Request $request): View
    {
        $recipient = Recipient::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isRecipientApproved($recipient)) {
            abort(Response::HTTP_FORBIDDEN, 'Feature locked until admin approval.');
        }

        return view('recipient.requests', ['recipient' => $recipient]);
    }

    public function editProfileRequest(Request $request): View
    {
        $recipient = Recipient::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isRecipientApproved($recipient)) {
            abort(Response::HTTP_FORBIDDEN, 'Feature locked until admin approval.');
        }

        return view('recipient.edit-profile', [
            'recipient' => $recipient,
            'recentRequests' => RecipientChangeRequest::where('recipient_id', $recipient->id)->latest()->limit(8)->get(),
        ]);
    }

    public function submitProfileChangeRequest(Request $request): RedirectResponse
    {
        $recipient = Recipient::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isRecipientApproved($recipient)) {
            return back()->with('error', 'Your recipient account is pending admin verification and approval.');
        }
        if (! $recipient->hospital_id) {
            return back()->with('error', 'No hospital is assigned to review this request.');
        }

        $request->merge([
            'organ_needed' => $request->filled('organ_needed') ? trim((string) $request->input('organ_needed')) : null,
            'region' => $request->filled('region') ? trim((string) $request->input('region')) : null,
            'organs_needed' => $request->filled('organs_needed') ? trim((string) $request->input('organs_needed')) : null,
            'reason' => $request->filled('reason') ? trim((string) $request->input('reason')) : null,
        ]);

        $validated = $request->validate([
            'blood_group' => ['required', Rule::in(['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'])],
            'organ_needed' => ['required', 'string', 'max:255'],
            'urgency_level' => ['required', Rule::in(['high', 'medium', 'low'])],
            'waiting_time' => ['required', 'integer', 'min:0', 'max:3650'],
            'region' => ['nullable', 'string', 'min:2', 'max:100'],
            'organs_needed' => ['nullable', 'string', 'max:255'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        RecipientChangeRequest::create([
            'recipient_id' => $recipient->id,
            'hospital_id' => $recipient->hospital_id,
            'requested_by_user_id' => $request->user()->id,
            'payload' => [
                'blood_group' => $validated['blood_group'],
                'organ_needed' => $validated['organ_needed'],
                'urgency_level' => $validated['urgency_level'],
                'waiting_time' => (int) $validated['waiting_time'],
                'region' => $validated['region'] ?? null,
                'organs_needed' => $validated['organs_needed']
                    ? array_map('trim', explode(',', $validated['organs_needed']))
                    : [$validated['organ_needed']],
                'reason' => $validated['reason'],
            ],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Profile correction request submitted to hospital for confirmation.');
    }

    public function matches(Request $request): View
    {
        $recipient = Recipient::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isRecipientApproved($recipient)) {
            abort(Response::HTTP_FORBIDDEN, 'Feature locked until admin approval.');
        }

        return view('recipient.matches', [
            'matches' => AllocationMatch::with('donor.user')
                ->where('recipient_id', $recipient->id)
                ->latest()
                ->paginate(10),
        ]);
    }

    public function editProfile(Request $request): View
    {
        $recipient = Recipient::where('user_id', $request->user()->id)->firstOrFail();
        
        return view('recipient.profile-edit', [
            'recipient' => $recipient,
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(UpdateRecipientProfileRequest $request): RedirectResponse
    {
        $recipient = Recipient::where('user_id', $request->user()->id)->firstOrFail();
        
        $validated = $request->validated();

        if (! empty($validated['full_name'])) {
            $request->user()->update(['name' => $validated['full_name']]);
        }

        $recipient->update([
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'blood_group' => $validated['blood_group'] ?? $recipient->blood_group,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
        ]);
        
        return back()->with('success', 'Profile updated successfully.');
    }

    public function requestEscalation(Request $request): RedirectResponse
    {
        return back()->with('error', 'Priority escalation is handled by the hospital for recipient accounts.');
    }

    public function createEmergencyRequest(Request $request): RedirectResponse
    {
        return back()->with('error', 'Emergency request creation is managed by the hospital module.');
    }

    private function isRecipientApproved(Recipient $recipient): bool
    {
        return (bool) $recipient->admin_approved && $recipient->status !== 'REJECTED';
    }
}
