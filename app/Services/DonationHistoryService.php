<?php

namespace App\Services;

use App\Models\AllocationMatch;
use App\Models\DonationHistory;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class DonationHistoryService
{
    public function __construct(
        private readonly BloodCompatibilityService $bloodCompatibilityService,
    ) {
    }

    public function recordDonation(AllocationMatch $match, Hospital $hospital, string $status): DonationHistory
    {
        $donor = $match->donor;
        $recipient = $match->recipient;
        $organType = $recipient->organ_needed;

        $alreadyDonatedSameOrgan = DonationHistory::where('donor_id', $donor->id)
            ->where('organ_type', $organType)
            ->where(function ($query) use ($recipient, $hospital): void {
                $query->where('recipient_id', '!=', $recipient->id)
                    ->orWhere('hospital_id', '!=', $hospital->id);
            })
            ->exists();
        if ($alreadyDonatedSameOrgan) {
            throw ValidationException::withMessages([
                'organ_type' => 'This donor has already donated the same organ.',
            ]);
        }

        if (! $this->bloodCompatibilityService->canDonateTo($donor->blood_group, $recipient->blood_group)) {
            throw ValidationException::withMessages([
                'blood_group' => 'Donor and recipient blood groups are not compatible.',
            ]);
        }

        return DonationHistory::updateOrCreate(
            [
                'donor_id' => $donor->id,
                'recipient_id' => $recipient->id,
                'hospital_id' => $hospital->id,
                'organ_type' => $organType,
            ],
            [
                'donation_date' => now()->toDateString(),
                'status' => $status,
            ]
        );
    }

    public function getAllDonations(): Collection
    {
        return DonationHistory::with(['donor.user', 'recipient.user', 'hospital.user'])
            ->latest('donation_date')
            ->get();
    }
}
