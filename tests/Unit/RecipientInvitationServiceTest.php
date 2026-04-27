<?php

namespace Tests\Unit;

use App\DTO\RegisterRecipientWithInviteData;
use App\Models\Hospital;
use App\Models\Recipient;
use App\Models\RecipientVerification;
use App\Models\User;
use App\Services\RecipientInvitationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RecipientInvitationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_invitation_link_is_rejected_using_expires_at(): void
    {
        $user = User::factory()->create(['role' => 'hospital']);
        $hospital = Hospital::create([
            'user_id' => $user->id,
            'name' => 'Test Hospital',
            'location' => 'City',
            'approved' => true,
        ]);

        $invite = RecipientVerification::create([
            'rvid' => 'RVID-EXPIRED-TEST',
            'hospital_id' => $hospital->id,
            'recipient_name' => 'Expired Recipient',
            'email' => 'expired@example.com',
            'phone' => '1234567890',
            'blood_group' => 'A+',
            'status' => 'Pending',
            'expires_at' => now()->subHour(),
        ]);

        $service = app(RecipientInvitationService::class);

        $this->expectException(ValidationException::class);
        $service->validateRvid($invite->rvid);
    }

    public function test_duplicate_identity_number_is_rejected_for_invite_registration(): void
    {
        $hospitalUser = User::factory()->create(['role' => 'hospital']);
        $hospital = Hospital::create([
            'user_id' => $hospitalUser->id,
            'name' => 'Test Hospital',
            'location' => 'City',
            'approved' => true,
        ]);

        Recipient::create([
            'user_id' => User::factory()->create(['role' => 'recipient'])->id,
            'hospital_id' => $hospital->id,
            'blood_group' => 'A+',
            'organ_needed' => 'Kidney',
            'urgency_level' => 'low',
            'waiting_time' => 0,
            'status' => 'REGISTERED',
            'identity_type' => 'aadhaar',
            'identity_number' => '1234-5678-9012',
            'identity_number_hash' => hash('sha256', '123456789012'),
        ]);

        RecipientVerification::create([
            'rvid' => 'RVID-DUPLICATE-TEST',
            'hospital_id' => $hospital->id,
            'recipient_name' => 'Duplicate ID Recipient',
            'email' => 'dup-recipient@example.com',
            'phone' => '1234567890',
            'blood_group' => 'A+',
            'status' => 'Pending',
            'expires_at' => now()->addHour(),
        ]);

        $service = app(RecipientInvitationService::class);

        $this->expectException(ValidationException::class);
        $service->registerWithInvite(new RegisterRecipientWithInviteData(
            'RVID-DUPLICATE-TEST',
            'aadhaar',
            '1234 5678 9012',
            'password123'
        ));
    }
}
