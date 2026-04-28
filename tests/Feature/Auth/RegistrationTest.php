<?php

namespace Tests\Feature\Auth;

use App\Models\Hospital;
use App\Models\RecipientVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'donor',
            'identity_type' => 'aadhaar',
            'identity_number' => 'NID-1234567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('donor.dashboard', absolute: false));
    }

    public function test_donor_registration_requires_identity_fields(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'missing-identity@example.com',
            'role' => 'donor',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['identity_type', 'identity_number']);
    }

    public function test_hospital_registration_uses_hospital_fields(): void
    {
        $response = $this->post('/register', [
            'name' => 'Hospital Admin',
            'email' => 'hospital@example.com',
            'role' => 'hospital',
            'hospital_name' => 'Kerala Care Hospital',
            'hospital_registration_id' => 'KL-HOSP-1001',
            'hospital_location' => 'Kochi, Kerala',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('hospital.dashboard', absolute: false));
        $this->assertDatabaseHas('hospitals', [
            'name' => 'Kerala Care Hospital',
            'location' => 'Kochi, Kerala',
            'identity_type' => 'hospital_registration_id',
            'identity_number' => 'KL-HOSP-1001',
        ]);
    }

    public function test_hospital_registration_requires_hospital_fields(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Hospital Admin',
            'email' => 'missing-hospital@example.com',
            'role' => 'hospital',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['hospital_name', 'hospital_registration_id', 'hospital_location']);
    }

    public function test_recipient_can_register_with_hospital_rvid(): void
    {
        $invite = $this->createRecipientInvite();

        $response = $this->post('/register', [
            'role' => 'recipient',
            'rvid' => $invite->rvid,
            'identity_type' => 'aadhaar',
            'identity_number' => '123456789012',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('recipient.dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => $invite->email,
            'role' => 'recipient',
        ]);
        $this->assertDatabaseHas('recipient_verifications', [
            'rvid' => $invite->rvid,
            'status' => 'Used',
        ]);
    }

    public function test_recipient_registration_requires_rvid(): void
    {
        $response = $this->from('/register')->post('/register', [
            'role' => 'recipient',
            'identity_type' => 'aadhaar',
            'identity_number' => '123456789012',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('rvid');
    }

    public function test_recipient_rvid_registration_validates_identity_number_format(): void
    {
        $invite = $this->createRecipientInvite();

        $response = $this->from('/register')->post('/register', [
            'role' => 'recipient',
            'rvid' => $invite->rvid,
            'identity_type' => 'aadhaar',
            'identity_number' => 'ABCDE1234F',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('identity_number');
    }

    public function test_registration_requires_role(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'missing-fields@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['role']);
    }

    private function createRecipientInvite(): RecipientVerification
    {
        $hospitalUser = User::factory()->create(['role' => 'hospital']);
        $hospital = Hospital::create([
            'user_id' => $hospitalUser->id,
            'name' => 'Kerala Care Hospital',
            'location' => 'Kochi, Kerala',
            'approved' => true,
        ]);

        return RecipientVerification::create([
            'rvid' => 'RVID-REGISTER-FORM-TEST',
            'hospital_id' => $hospital->id,
            'recipient_name' => 'Recipient User',
            'email' => 'recipient-rvid@example.com',
            'phone' => '9876543210',
            'blood_group' => 'A+',
            'status' => 'Pending',
            'expires_at' => now()->addHour(),
        ]);
    }
}
