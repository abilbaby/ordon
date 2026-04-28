<?php

namespace Tests\Feature;

use App\Models\Hospital;
use App\Models\RecipientVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipientInviteRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invite_registration_rejects_invalid_identity_number_for_type(): void
    {
        $invite = $this->createInvite();

        $response = $this->post('/recipient/register', [
            'rvid' => $invite->rvid,
            'identity_type' => 'aadhaar',
            'identity_number' => 'ABCDE1234F',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('identity_number');
        $this->assertGuest();
    }

    public function test_invite_registration_sanitizes_identity_number_before_saving(): void
    {
        $invite = $this->createInvite(['email' => 'pan-recipient@example.com']);

        $response = $this->post('/recipient/register', [
            'rvid' => $invite->rvid,
            'identity_type' => 'pan',
            'identity_number' => 'abcde 1234 f',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('recipient.dashboard', absolute: false));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('recipients', [
            'identity_type' => 'pan',
            'identity_number' => 'ABCDE1234F',
        ]);
    }

    private function createInvite(array $attributes = []): RecipientVerification
    {
        $hospitalUser = User::factory()->create(['role' => 'hospital']);
        $hospital = Hospital::create([
            'user_id' => $hospitalUser->id,
            'name' => 'Test Hospital',
            'location' => 'City',
            'approved' => true,
        ]);

        return RecipientVerification::create(array_merge([
            'rvid' => 'RVID-TEST-'.uniqid(),
            'hospital_id' => $hospital->id,
            'recipient_name' => 'Test Recipient',
            'email' => 'recipient@example.com',
            'phone' => '1234567890',
            'blood_group' => 'A+',
            'status' => 'Pending',
            'expires_at' => now()->addHour(),
        ], $attributes));
    }
}
