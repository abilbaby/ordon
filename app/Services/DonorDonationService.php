<?php

namespace App\Services;

use App\DTO\DonorDonationPreferencesData;
use App\Enums\DonationType;
use App\Enums\OrganType;
use App\Models\Donor;
use App\Models\DonorOrgan;
use Illuminate\Validation\ValidationException;

class DonorDonationService
{
    public function addOrUpdateDonationPreferences(Donor $donor, DonorDonationPreferencesData $data): void
    {
        $organs = array_values(array_unique($data->organs));

        if (count($organs) === 0) {
            throw ValidationException::withMessages(['organs' => 'Please select at least one organ.']);
        }

        if ($data->donationType === DonationType::LivingDonation) {
            $invalid = array_diff($organs, OrganType::livingAllowedValues());
            if ($invalid !== []) {
                throw ValidationException::withMessages([
                    'organs' => 'Living donation allows only Kidney, Liver, and Lung.',
                ]);
            }
        }

        $donor->update([
            'donation_type' => $data->donationType->value,
            'is_available' => $data->isAvailable,
            'notes' => $data->notes,
            'organ_type' => $organs[0], // Backward compatibility for existing matching logic.
        ]);

        $donor->organs()->delete();
        foreach ($organs as $organ) {
            DonorOrgan::create([
                'donor_id' => $donor->id,
                'organ_type' => $organ,
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    public function getDonorOrgans(Donor $donor): array
    {
        return $donor->organs()->pluck('organ_type')->all();
    }

    public function toggleAvailability(Donor $donor): void
    {
        $donor->update(['is_available' => ! $donor->is_available]);
    }
}
