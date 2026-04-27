<?php

namespace Tests\Feature;

use App\Models\AllocationMatch;
use App\Models\Donor;
use App\Models\Hospital;
use App\Models\Recipient;
use App\Models\Transplant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HospitalAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hospital_cannot_update_certificate_name_for_another_hospitals_transplant(): void
    {
        $donorUser = User::factory()->create(['role' => 'donor']);
        $recipientUser = User::factory()->create(['role' => 'recipient']);
        $hospitalOwnerUser = User::factory()->create(['role' => 'hospital']);
        $otherHospitalUser = User::factory()->create(['role' => 'hospital']);

        $donor = Donor::create([
            'user_id' => $donorUser->id,
            'blood_group' => 'O+',
            'organ_type' => 'Kidney',
            'medical_status' => 'VERIFIED',
            'is_available' => true,
            'approved' => true,
            'identity_verified' => true,
        ]);
        $recipient = Recipient::create([
            'user_id' => $recipientUser->id,
            'blood_group' => 'A+',
            'organ_needed' => 'Kidney',
            'urgency_level' => 'high',
            'waiting_time' => 100,
            'status' => 'MATCHED',
            'admin_approved' => true,
        ]);

        $ownerHospital = Hospital::create([
            'user_id' => $hospitalOwnerUser->id,
            'name' => 'Owner Hospital',
            'location' => 'City',
            'approved' => true,
            'identity_verified' => true,
        ]);
        Hospital::create([
            'user_id' => $otherHospitalUser->id,
            'name' => 'Other Hospital',
            'location' => 'City',
            'approved' => true,
            'identity_verified' => true,
        ]);

        $match = AllocationMatch::create([
            'donor_id' => $donor->id,
            'recipient_id' => $recipient->id,
            'score' => 90,
            'match_score' => 90,
            'status' => 'APPROVED',
        ]);

        $transplant = Transplant::create([
            'match_id' => $match->id,
            'hospital_id' => $ownerHospital->id,
            'status' => 'APPROVED',
        ]);

        $response = $this->actingAs($otherHospitalUser)
            ->post(route('hospital.transplants.certificate-recipient', $transplant), [
                'recipient_name_override' => 'Unauthorized Update',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('transplants', [
            'id' => $transplant->id,
            'recipient_name_override' => 'Unauthorized Update',
        ]);
    }
}
