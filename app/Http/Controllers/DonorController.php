<?php

namespace App\Http\Controllers;

use App\DTO\DonorDonationPreferencesData;
use App\Enums\DonationType;
use App\Enums\OrganType;
use App\Models\Donor;
use App\Models\AllocationMatch;
use App\Models\DonationHistory;
use App\Models\EmergencyRequest;
use App\Models\IssueReport;
use App\Models\StatusHistory;
use App\Models\Transplant;
use App\Models\UserNotification;
use App\Services\AuditLogger;
use App\Services\AllocationEngine;
use App\Services\DonorDonationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DonorController extends Controller
{
    public function dashboard(Request $request): View
    {
        $donor = Donor::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'blood_group' => 'O+',
                'organ_type' => OrganType::Kidney->value,
                'medical_status' => 'REGISTERED',
                'is_available' => true,
                'donation_type' => DonationType::LivingDonation->value,
            ]
        );
        if (in_array($donor->donation_type, ['living', 'deceased'], true)) {
            $donor->update([
                'donation_type' => $donor->donation_type === 'living'
                    ? DonationType::LivingDonation->value
                    : DonationType::AfterDeathDonation->value,
            ]);
            $donor->refresh();
        }
        if (! $donor->organs()->exists()) {
            $donor->organs()->create([
                'organ_type' => $donor->organ_type ?: OrganType::Kidney->value,
            ]);
        }

        return view('donor.dashboard', [
            'donor' => $donor,
            'selectedOrgans' => $donor->organs()->pluck('organ_type')->all(),
            'donationTypes' => DonationType::values(),
            'organOptions' => OrganType::values(),
            'accountApproved' => $this->isDonorApproved($donor),
            'recentMatches' => AllocationMatch::with('recipient.user')
                ->where('donor_id', $donor->id)
                ->latest()
                ->limit(5)
                ->get(),
            'activities' => AllocationMatch::with('recipient.user')
                ->where('donor_id', $donor->id)
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn (AllocationMatch $match): array => [
                    'label' => "Matched with ".($match->recipient->user->name ?? 'Recipient')." ({$match->status})",
                    'time' => $match->created_at,
                    'type' => $match->status === 'APPROVED' ? 'success' : ($match->status === 'REJECTED' ? 'danger' : 'info'),
                ]),
            'emergencyRequests' => EmergencyRequest::where('status', 'open')
                ->where('blood_group', $donor->blood_group)
                ->latest()
                ->limit(5)
                ->get(),
            'reports' => IssueReport::where('user_id', $request->user()->id)->latest()->limit(5)->get(),
            'donationHistory' => DonationHistory::with(['recipient.user', 'hospital'])
                ->where('donor_id', $donor->id)
                ->latest('donation_date')
                ->limit(10)
                ->get(),
            'statusHistories' => StatusHistory::with('user')
                ->where('entity_type', Donor::class)
                ->where('entity_id', $donor->id)
                ->latest('changed_at')
                ->limit(10)
                ->get(),
            'notificationPreview' => UserNotification::where('user_id', $request->user()->id)->latest()->limit(5)->get(),
        ]);
    }

    public function updateProfile(Request $request, DonorDonationService $donorDonationService): RedirectResponse
    {
        $donor = Donor::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isDonorApproved($donor)) {
            return back()->with('error', 'Your donor account is pending admin verification and approval.');
        }

        $validated = $request->validate([
            'blood_group' => ['required', 'in:O-,O+,A-,A+,B-,B+,AB-,AB+'],
            'donation_type' => ['required', Rule::in(DonationType::values())],
            'organs' => ['required', 'array', 'min:1'],
            'organs.*' => ['required', Rule::in(OrganType::values())],
            'region' => ['nullable', 'string', 'max:255'],
            'medical_conditions' => ['nullable', 'string', 'max:2000'],
            'family_contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_available' => ['nullable', 'boolean'],
            'available_until' => ['nullable', 'date', 'after:now'],
        ]);

        $donorDonationService->addOrUpdateDonationPreferences(
            $donor,
            new DonorDonationPreferencesData(
                DonationType::from($validated['donation_type']),
                $validated['organs'],
                (bool) ($validated['is_available'] ?? $donor->is_available),
                $validated['notes'] ?? null,
            )
        );

        unset($validated['donation_type'], $validated['organs'], $validated['is_available'], $validated['notes']);
        $validated['eligibility_status'] = $this->resolveEligibilityStatus($donor, $validated);
        $donor->update($validated);

        return back()->with('success', 'Donation preferences and profile updated.');
    }

    public function toggleAvailability(Request $request, DonorDonationService $donorDonationService): RedirectResponse
    {
        $donor = Donor::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isDonorApproved($donor)) {
            return back()->with('error', 'Your donor account is pending admin verification and approval.');
        }
        $donorDonationService->toggleAvailability($donor);

        return back()->with('success', 'Donor availability updated.');
    }

    public function runMatch(Request $request, AllocationEngine $allocationEngine): RedirectResponse
    {
        $donor = Donor::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isDonorApproved($donor)) {
            return back()->with('error', 'Your donor account is pending admin verification and approval.');
        }
        $match = $allocationEngine->findBestMatch($donor);

        if (! $match) {
            return back()->with('error', 'No compatible verified recipient found or donor is not eligible.');
        }

        return back()->with('success', 'Match found successfully.');
    }

    public function matches(Request $request): View
    {
        $donor = Donor::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isDonorApproved($donor)) {
            abort(Response::HTTP_FORBIDDEN, 'Feature locked until admin approval.');
        }

        return view('donor.matches', [
            'matches' => AllocationMatch::with('recipient.user')
                ->where('donor_id', $donor->id)
                ->latest()
                ->paginate(10),
        ]);
    }

    public function certificate(Request $request): View
    {
        $donor = Donor::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isDonorApproved($donor)) {
            abort(Response::HTTP_FORBIDDEN, 'Feature locked until admin approval.');
        }
        $completedTransplant = Transplant::with(['hospital', 'match.recipient.user'])
            ->whereHas('match', fn ($query) => $query->where('donor_id', $donor->id))
            ->where('status', 'COMPLETED')
            ->latest()
            ->first();

        if (! $completedTransplant) {
            abort(Response::HTTP_FORBIDDEN, 'Certificate is available only after donation completion.');
        }
        if (! $completedTransplant->certificate_id) {
            $completedTransplant->update(['certificate_id' => $this->buildCertificateId($completedTransplant->id)]);
            $completedTransplant->refresh();
        }

        return view('donor.certificate', [
            'donor' => $donor,
            'completedTransplant' => $completedTransplant,
            'certificateId' => $completedTransplant->certificate_id,
        ]);
    }

    public function downloadCertificate(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $donor = Donor::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isDonorApproved($donor)) {
            abort(Response::HTTP_FORBIDDEN, 'Feature locked until admin approval.');
        }
        $completedTransplant = Transplant::with(['hospital', 'match.recipient.user'])
            ->whereHas('match', fn ($query) => $query->where('donor_id', $donor->id))
            ->where('status', 'COMPLETED')
            ->latest()
            ->first();
        if (! $completedTransplant) {
            abort(Response::HTTP_FORBIDDEN, 'Certificate is available only after donation completion.');
        }
        if (! $completedTransplant->certificate_id) {
            $completedTransplant->update(['certificate_id' => $this->buildCertificateId($completedTransplant->id)]);
            $completedTransplant->refresh();
        }

        return Pdf::loadView('donor.certificate-pdf', [
            'donor' => $donor,
            'user' => $request->user(),
            'issuedAt' => now(),
            'completedTransplant' => $completedTransplant,
            'certificateId' => $completedTransplant->certificate_id,
        ])->setPaper('a4', 'landscape')
            ->download('ordon-donor-certificate-'.$request->user()->id.'.pdf');
    }

    public function updateConsent(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $donor = Donor::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isDonorApproved($donor)) {
            return back()->with('error', 'Your donor account is pending admin verification and approval.');
        }

        $validated = $request->validate([
            'consent_given' => ['required', 'boolean'],
            'pre_donation_checklist_completed' => ['required', 'boolean'],
        ]);

        $donor->update([
            ...$validated,
            'eligibility_status' => $this->resolveEligibilityStatus($donor, $validated),
        ]);

        $auditLogger->log($request->user(), 'donor', 'update_consent', 'Consent or checklist updated');

        return back()->with('success', 'Consent and checklist updated.');
    }

    public function acceptEmergencyRequest(
        Request $request,
        EmergencyRequest $emergencyRequest,
        AuditLogger $auditLogger
    ): RedirectResponse {
        $donor = Donor::where('user_id', $request->user()->id)->firstOrFail();
        if (! $this->isDonorApproved($donor)) {
            return back()->with('error', 'Your donor account is pending admin verification and approval.');
        }
        if ($emergencyRequest->status !== 'open') {
            return back()->with('error', 'This emergency request is no longer open.');
        }

        $emergencyRequest->update([
            'status' => 'accepted',
            'accepted_donor_id' => $donor->id,
        ]);

        $auditLogger->log($request->user(), 'donor', 'accept_emergency_request', "Request {$emergencyRequest->id} accepted");

        return back()->with('success', 'Emergency donation request accepted.');
    }

    private function resolveEligibilityStatus(Donor $donor, array $input): string
    {
        $consent = (bool) ($input['consent_given'] ?? $donor->consent_given);
        $checklist = (bool) ($input['pre_donation_checklist_completed'] ?? $donor->pre_donation_checklist_completed);
        $blacklisted = (bool) ($input['blacklisted'] ?? $donor->blacklisted);

        if ($blacklisted) {
            return 'ineligible';
        }

        return $consent && $checklist ? 'eligible' : 'pending';
    }

    private function isDonorApproved(Donor $donor): bool
    {
        return $donor->identity_verified && $donor->approved && $donor->medical_status === 'VERIFIED';
    }

    private function buildCertificateId(int $transplantId): string
    {
        return 'ORDON-CERT-'.now()->format('Ymd').'-'.$transplantId.'-'.Str::upper(Str::random(6));
    }
}
