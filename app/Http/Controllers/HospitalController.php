<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTransplantSlotRequest;
use App\Http\Requests\HospitalCreateInviteRequest;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Enums\OrganType;
use App\Models\AllocationMatch;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\IssueReport;
use App\Models\OrganInventory;
use App\Models\Recipient;
use App\Models\RecipientChangeRequest;
use App\Models\RecipientVerification;
use App\Models\SystemSetting;
use App\Models\Transplant;
use App\Models\UserNotification;
use App\Models\StatusHistory;
use App\DTO\CreateRecipientInviteData;
use App\Services\AuditLogger;
use App\Services\DonationHistoryService;
use App\Services\NotificationService;
use App\Services\RecipientInvitationService;
use App\Services\WorkflowStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HospitalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $hospital = Hospital::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['name' => 'ORDON Medical Center', 'location' => 'Dhaka', 'approved' => true]
        );

        $pendingMatches = AllocationMatch::with(['donor.user', 'recipient.user'])
            ->where('status', 'MATCHED')
            ->latest()
            ->get();
        $recentTransplants = Transplant::with(['match.donor.user', 'match.recipient.user'])
            ->where('hospital_id', $hospital->id)
            ->latest()
            ->limit(6)
            ->get();

        return view('hospital.dashboard', [
            'hospital' => $hospital,
            'accountApproved' => $this->isHospitalApproved($hospital),
            'pendingMatches' => $pendingMatches,
            'recentTransplants' => $recentTransplants,
            'stats' => [
                'pending' => AllocationMatch::where('status', 'MATCHED')->count(),
                'approved' => Transplant::where('hospital_id', $hospital->id)->where('status', 'APPROVED')->count(),
                'completed' => Transplant::where('hospital_id', $hospital->id)->where('status', 'COMPLETED')->count(),
            ],
            'activities' => collect()
                ->merge($pendingMatches->take(5)->map(fn (AllocationMatch $match): array => [
                    'label' => "Pending approval: ".($match->donor->user->name ?? 'Donor')." -> ".($match->recipient->user->name ?? 'Recipient'),
                    'time' => $match->created_at,
                    'type' => 'warning',
                ]))
                ->merge($recentTransplants->take(5)->map(fn (Transplant $transplant): array => [
                    'label' => "Transplant {$transplant->status}",
                    'time' => $transplant->created_at,
                    'type' => $transplant->status === 'COMPLETED' ? 'success' : 'info',
                ]))
                ->sortByDesc('time')
                ->take(8)
                ->values(),
            'doctors' => Doctor::where('hospital_id', $hospital->id)->latest()->limit(6)->get(),
            'inventory' => OrganInventory::where('hospital_id', $hospital->id)->orderBy('organ_type')->get(),
            'pendingRecipientApprovals' => Recipient::where('hospital_verified', false)
                ->where('hospital_id', $hospital->id)
                ->where('admin_approved', false)
                ->where('status', 'REGISTERED')
                ->limit(6)
                ->get(),
            'recipientChangeRequests' => RecipientChangeRequest::with(['recipient.user', 'requestedBy'])
                ->where('hospital_id', $hospital->id)
                ->where('status', 'pending')
                ->latest()
                ->limit(8)
                ->get(),
            'recipientInvites' => RecipientVerification::where('hospital_id', $hospital->id)->latest()->limit(10)->get(),
            'reports' => IssueReport::where('user_id', $request->user()->id)->latest()->limit(5)->get(),
            'donationHistory' => \App\Models\DonationHistory::with(['donor.user', 'recipient.user'])
                ->where('hospital_id', $hospital->id)
                ->latest('donation_date')
                ->limit(8)
                ->get(),
            'statusHistories' => StatusHistory::with('user')
                ->where(function ($query): void {
                    $query->where('entity_type', AllocationMatch::class)
                        ->orWhere('entity_type', Transplant::class);
                })
                ->latest('changed_at')
                ->limit(12)
                ->get(),
            'notificationPreview' => UserNotification::where('user_id', $request->user()->id)->latest()->limit(5)->get(),
        ]);
    }

    public function invitations(Request $request): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            abort(403, 'Feature locked until admin approval.');
        }

        return view('hospital.invitations', [
            'recipientInvites' => RecipientVerification::where('hospital_id', $hospital->id)
                ->latest()
                ->paginate(12),
            'hospital' => $hospital,
        ]);
    }

    public function approveMatch(
        Request $request,
        AllocationMatch $match,
        WorkflowStatusService $workflowStatusService,
        DonationHistoryService $donationHistoryService,
        NotificationService $notificationService
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        $settings = SystemSetting::firstOrCreate(['id' => 1], ['max_daily_surgeries' => 6]);
        $todayScheduled = Transplant::where('hospital_id', $hospital->id)
            ->whereDate('scheduled_at', today())
            ->count();
        if ($todayScheduled >= (int) $settings->max_daily_surgeries) {
            return back()->with('error', 'Daily surgery cap reached from admin settings.');
        }
        if ($match->status !== 'MATCHED') {
            return back()->with('error', 'Only MATCHED records can be approved.');
        }

        try {
            $workflowStatusService->advanceStatus($match, 'status', 'APPROVED');
        } catch (\InvalidArgumentException|ValidationException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        Transplant::create([
            'match_id' => $match->id,
            'hospital_id' => $hospital->id,
            'status' => 'APPROVED',
            'scheduled_at' => now()->addDays(2),
        ]);
        $donationHistoryService->recordDonation($match->load(['donor', 'recipient']), $hospital, 'Scheduled');
        if ($match->donor?->user) {
            $notificationService->notify(
                $match->donor->user,
                'approval',
                'Match approved by hospital',
                "Hospital {$hospital->name} approved your match #{$match->id}.",
                AllocationMatch::class,
                $match->id
            );
        }

        return back()->with('success', 'Approval updated and transplant scheduled.');
    }

    public function completeTransplant(
        AllocationMatch $match,
        WorkflowStatusService $workflowStatusService,
        DonationHistoryService $donationHistoryService,
        NotificationService $notificationService
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', auth()->id())->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        $transplant = Transplant::where('match_id', $match->id)->latest()->first();

        if (! $transplant) {
            return back()->with('error', 'No transplant record found for this match.');
        }
        if ($transplant->hospital_id !== $hospital->id) {
            return back()->with('error', 'You are not authorized for this transplant.');
        }

        try {
            $workflowStatusService->advanceStatus($transplant, 'status', 'COMPLETED');
            $workflowStatusService->advanceStatus($match, 'status', 'COMPLETED');
            $donationHistoryService->recordDonation($match->load(['donor', 'recipient']), $hospital, 'Completed');
            if ($match->recipient?->user) {
                $notificationService->notify(
                    $match->recipient->user,
                    'completed',
                    'Transplant completed',
                    "Your transplant for match #{$match->id} has been marked completed.",
                    AllocationMatch::class,
                    $match->id
                );
            }
        } catch (\InvalidArgumentException|ValidationException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Transplant marked as completed.');
    }

    public function rejectMatch(AllocationMatch $match, NotificationService $notificationService): RedirectResponse
    {
        $hospital = Hospital::where('user_id', auth()->id())->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        $transplant = Transplant::where('match_id', $match->id)->latest()->first();
        if ($transplant && $transplant->hospital_id !== $hospital->id) {
            return back()->with('error', 'You are not authorized to reject this match.');
        }
        $match->update(['status' => 'REJECTED']);
        if ($match->donor?->user) {
            $notificationService->notify(
                $match->donor->user,
                'rejected',
                'Match rejected',
                "Match #{$match->id} was rejected during hospital review.",
                AllocationMatch::class,
                $match->id
            );
        }

        return back()->with('success', 'Approval updated: match rejected.');
    }

    public function approvals(): View
    {
        $hospital = Hospital::where('user_id', auth()->id())->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            abort(403, 'Feature locked until admin approval.');
        }

        return view('hospital.approvals', [
            'matches' => AllocationMatch::with(['donor.user', 'recipient.user'])
                ->where('status', 'MATCHED')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function transplants(Request $request): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            abort(403, 'Feature locked until admin approval.');
        }

        return view('hospital.transplants', [
            'transplants' => Transplant::with(['match.donor.user', 'match.recipient.user'])
                ->where('hospital_id', $hospital->id)
                ->latest()
                ->paginate(12),
        ]);
    }

    public function planner(Request $request): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            abort(403, 'Feature locked until admin approval.');
        }
        $upcoming = Transplant::with(['match.donor.user', 'match.recipient.user', 'doctor'])
            ->where('hospital_id', $hospital->id)
            ->whereIn('status', ['APPROVED', 'COMPLETED'])
            ->latest()
            ->paginate(12);

        return view('hospital.planner', [
            'upcoming' => $upcoming,
            'hospital' => $hospital->load('doctors'),
        ]);
    }

    public function assignSlot(AssignTransplantSlotRequest $request, Transplant $transplant): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($transplant->hospital_id !== $hospital->id) {
            return back()->with('error', 'You are not authorized for this transplant.');
        }

        $validated = $request->validated();
        $doctor = Doctor::where('id', $validated['doctor_id'])
            ->where('hospital_id', $hospital->id)
            ->firstOrFail();

        $transplant->update([
            'slot_date' => $validated['slot_date'],
            'slot_period' => $validated['slot_period'],
            'operating_room' => $validated['operating_room'],
            'doctor_id' => $doctor->id,
            'surgeon_name' => $doctor->name,
            'scheduled_at' => now(),
        ]);

        return back()->with('success', 'Hospital slot assigned successfully.');
    }

    public function addDoctor(StoreDoctorRequest $request, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        $validated = $request->validated();
        Doctor::create([...$validated, 'hospital_id' => $hospital->id]);
        $auditLogger->log($request->user(), 'hospital', 'add_doctor', $validated['name']);

        return back()->with('success', 'Doctor added successfully.');
    }

    public function editDoctor(Request $request, Doctor $doctor): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            abort(403, 'Feature locked until admin approval.');
        }
        if ($doctor->hospital_id !== $hospital->id) {
            abort(403, 'You can only edit doctors from your hospital.');
        }

        return view('hospital.doctors.edit', ['doctor' => $doctor, 'hospital' => $hospital]);
    }

    public function updateDoctor(UpdateDoctorRequest $request, Doctor $doctor, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($doctor->hospital_id !== $hospital->id) {
            return back()->with('error', 'You can only edit doctors from your hospital.');
        }

        $validated = $request->validated();
        $doctor->update($validated);
        $auditLogger->log($request->user(), 'hospital', 'update_doctor', "Doctor {$doctor->id} updated");

        return back()->with('success', 'Doctor updated successfully.');
    }

    public function deleteDoctor(Request $request, Doctor $doctor, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($doctor->hospital_id !== $hospital->id) {
            return back()->with('error', 'You can only delete doctors from your hospital.');
        }

        $doctorName = $doctor->name;
        $doctor->delete();
        $auditLogger->log($request->user(), 'hospital', 'delete_doctor', "Doctor {$doctorName} deleted");

        return back()->with('success', 'Doctor deleted successfully.');
    }

    public function addInventory(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        $validated = $request->validate([
            'organ_type' => ['required', Rule::in(OrganType::values())],
            'units' => ['required', 'integer', 'min:0', 'max:999'],
        ]);
        OrganInventory::updateOrCreate(
            ['hospital_id' => $hospital->id, 'organ_type' => $validated['organ_type']],
            ['units' => $validated['units']]
        );
        $auditLogger->log($request->user(), 'hospital', 'update_inventory', "{$validated['organ_type']}={$validated['units']}");

        return back()->with('success', 'Organ inventory updated.');
    }

    public function validateMatch(Request $request, AllocationMatch $match, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        $transplant = Transplant::where('match_id', $match->id)->latest()->first();
        if ($transplant && $transplant->hospital_id !== $hospital->id) {
            return back()->with('error', 'You are not authorized for this match.');
        }
        $match->update(['status' => 'APPROVED']);
        $auditLogger->log($request->user(), 'hospital', 'validate_match', "Match {$match->id} validated");

        return back()->with('success', 'Match medically validated by hospital.');
    }

    public function createRecipientInvite(
        HospitalCreateInviteRequest $request,
        RecipientInvitationService $recipientInvitationService,
        AuditLogger $auditLogger,
        NotificationService $notificationService
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        $validated = $request->validated();

        $invite = $recipientInvitationService->createRecipientInvite(
            $hospital,
            new CreateRecipientInviteData(
                $validated['recipient_name'],
                $validated['email'],
                $validated['phone'],
                $validated['blood_group'],
                $validated['notes'] ?? null,
                $validated['date_of_birth'] ?? null,
                $validated['gender'] ?? null,
                $validated['organ_needed'] ?? null,
                $validated['urgency_level'] ?? null,
                $validated['waiting_time'] ?? null,
                $validated['other_organs_needed'] ?? null,
                $validated['medical_notes'] ?? null,
                $validated['contact_number'] ?? null
            )
        );

        $auditLogger->log($request->user(), 'hospital', 'create_recipient_invite', "Invite {$invite->rvid} created");
        $notificationService->notify(
            $request->user(),
            'invite',
            'Recipient invite generated',
            "Invite {$invite->rvid} created and sent.",
            RecipientVerification::class,
            $invite->id
        );

        return back()->with('success', 'Recipient invitation link generated and sent via email.');
    }

    public function approveRecipient(
        Request $request,
        Recipient $recipient,
        RecipientInvitationService $recipientInvitationService,
        AuditLogger $auditLogger
    ): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($recipient->hospital_id !== $hospital->id) {
            return back()->with('error', 'You can approve only recipients invited by your hospital.');
        }
        $recipientInvitationService->approveRecipientByHospital($recipient);
        $auditLogger->log($request->user(), 'hospital', 'approve_recipient', "Recipient {$recipient->id} hospital-verified");

        return back()->with('success', 'Recipient approved by hospital. Admin approval has been auto-updated.');
    }

    public function rejectRecipient(
        Request $request,
        Recipient $recipient,
        RecipientInvitationService $recipientInvitationService,
        AuditLogger $auditLogger
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($recipient->hospital_id !== $hospital->id) {
            return back()->with('error', 'You can reject only recipients invited by your hospital.');
        }

        $recipientInvitationService->rejectRecipientByHospital($recipient);
        $auditLogger->log($request->user(), 'hospital', 'reject_recipient', "Recipient {$recipient->id} rejected");

        return back()->with('success', 'Recipient rejected.');
    }

    public function approveRecipientChangeRequest(
        Request $request,
        RecipientChangeRequest $changeRequest,
        AuditLogger $auditLogger
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($changeRequest->hospital_id !== $hospital->id || $changeRequest->status !== 'pending') {
            return back()->with('error', 'This request cannot be approved.');
        }

        $payload = $changeRequest->payload ?? [];
        $changeRequest->recipient->update([
            'blood_group' => $payload['blood_group'] ?? $changeRequest->recipient->blood_group,
            'organ_needed' => $payload['organ_needed'] ?? $changeRequest->recipient->organ_needed,
            'urgency_level' => $payload['urgency_level'] ?? $changeRequest->recipient->urgency_level,
            'waiting_time' => $payload['waiting_time'] ?? $changeRequest->recipient->waiting_time,
            'region' => $payload['region'] ?? $changeRequest->recipient->region,
            'organs_needed' => $payload['organs_needed'] ?? $changeRequest->recipient->organs_needed,
        ]);
        $changeRequest->update([
            'status' => 'approved',
            'hospital_note' => 'Approved by hospital',
        ]);
        $auditLogger->log($request->user(), 'hospital', 'approve_recipient_change_request', "Change request {$changeRequest->id} approved");

        return back()->with('success', 'Recipient details updated successfully from approved change request.');
    }

    public function rejectRecipientChangeRequest(
        Request $request,
        RecipientChangeRequest $changeRequest,
        AuditLogger $auditLogger
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($changeRequest->hospital_id !== $hospital->id || $changeRequest->status !== 'pending') {
            return back()->with('error', 'This request cannot be rejected.');
        }

        $changeRequest->update([
            'status' => 'rejected',
            'hospital_note' => 'Rejected by hospital',
        ]);
        $auditLogger->log($request->user(), 'hospital', 'reject_recipient_change_request', "Change request {$changeRequest->id} rejected");

        return back()->with('success', 'Recipient change request rejected.');
    }

    public function updateSurgeryWorkflow(
        Request $request,
        Transplant $transplant,
        AuditLogger $auditLogger
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($transplant->hospital_id !== $hospital->id) {
            return back()->with('error', 'You are not authorized for this transplant.');
        }
        $validated = $request->validate([
            'surgery_status' => ['required', 'in:Scheduled,In Progress,Completed'],
        ]);

        $transplant->update(['surgery_status' => $validated['surgery_status']]);
        $auditLogger->log($request->user(), 'hospital', 'surgery_workflow', "Transplant {$transplant->id}: {$validated['surgery_status']}");

        return back()->with('success', 'Surgery workflow updated.');
    }

    public function updateTransport(
        Request $request,
        Transplant $transplant,
        AuditLogger $auditLogger
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($transplant->hospital_id !== $hospital->id) {
            return back()->with('error', 'You are not authorized for this transplant.');
        }
        $validated = $request->validate([
            'transport_status' => ['required', 'in:Pending,In Transit,Delivered'],
        ]);

        $transplant->update(['transport_status' => $validated['transport_status']]);
        $auditLogger->log($request->user(), 'hospital', 'transport_update', "Transplant {$transplant->id}: {$validated['transport_status']}");

        return back()->with('success', 'Transport tracking updated.');
    }

    public function addPostOperationReport(
        Request $request,
        Transplant $transplant,
        AuditLogger $auditLogger
    ): RedirectResponse {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($transplant->hospital_id !== $hospital->id) {
            return back()->with('error', 'You are not authorized for this transplant.');
        }
        $validated = $request->validate([
            'post_operation_report' => ['required', 'string', 'max:3000'],
        ]);

        $transplant->update(['post_operation_report' => $validated['post_operation_report']]);
        $auditLogger->log($request->user(), 'hospital', 'post_op_report', "Transplant {$transplant->id} report added");

        return back()->with('success', 'Post-operation report saved.');
    }

    public function setCertificateRecipientName(Request $request, Transplant $transplant): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isHospitalApproved($hospital)) {
            return back()->with('error', 'Your hospital account is pending admin verification and approval.');
        }
        if ($transplant->hospital_id !== $hospital->id) {
            return back()->with('error', 'You are not authorized for this transplant.');
        }
        $request->merge([
            'recipient_name_override' => $request->filled('recipient_name_override')
                ? trim((string) $request->input('recipient_name_override'))
                : null,
        ]);

        $validated = $request->validate([
            'recipient_name_override' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
        ]);

        $transplant->update(['recipient_name_override' => $validated['recipient_name_override']]);

        return back()->with('success', 'Certificate recipient name updated.');
    }

    private function isHospitalApproved(Hospital $hospital): bool
    {
        return $hospital->identity_verified && $hospital->approved;
    }
}
