<?php

namespace Tests\Feature;

use App\Models\AllocationMatch;
use App\Models\Doctor;
use App\Models\Donor;
use App\Models\Hospital;
use App\Models\OrganInventory;
use App\Models\Recipient;
use App\Models\Transplant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OperationalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipient_can_update_editable_profile_fields_without_changing_identity_number(): void
    {
        $user = User::factory()->create(['role' => 'recipient', 'name' => 'Old Name']);
        Recipient::create([
            'user_id' => $user->id,
            'blood_group' => 'A+',
            'organ_needed' => 'Kidney',
            'urgency_level' => 'medium',
            'identity_type' => 'Aadhaar',
            'identity_number' => '123456789012',
        ]);

        $response = $this->actingAs($user)->patch(route('recipient.profile.update'), [
            'full_name' => 'Anaya Menon',
            'phone' => '+91 98765 43210',
            'address' => 'Kochi Medical Road',
            'date_of_birth' => '1994-05-10',
            'gender' => 'female',
            'blood_group' => 'O+',
            'emergency_contact_name' => 'Ravi Menon',
            'emergency_contact_phone' => '+91 99887 76655',
            'identity_number' => '999999999999',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Anaya Menon',
        ]);
        $this->assertDatabaseHas('recipients', [
            'user_id' => $user->id,
            'phone' => '919876543210',
            'address' => 'Kochi Medical Road',
            'identity_number' => '123456789012',
            'emergency_contact_phone' => '919988776655',
        ]);
    }

    public function test_hospital_invite_stores_expanded_recipient_fields(): void
    {
        Mail::fake();

        [$hospitalUser] = $this->createApprovedHospital();

        $response = $this->actingAs($hospitalUser)->post(route('hospital.recipient-invites.create'), [
            'recipient_name' => 'Devika Nair',
            'email' => 'devika@example.com',
            'phone' => '+91 98765 43210',
            'date_of_birth' => '1988-02-15',
            'gender' => 'female',
            'blood_group' => 'B+',
            'organ_needed' => 'Liver',
            'urgency_level' => 'high',
            'medical_notes' => 'Requires urgent review',
            'contact_number' => '+91 91234 56789',
            'notes' => 'Prepared for registration',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('recipient_verifications', [
            'recipient_name' => 'Devika Nair',
            'email' => 'devika@example.com',
            'phone' => '919876543210',
            'blood_group' => 'B+',
            'organ_needed' => 'Liver',
            'urgency_level' => 'high',
            'medical_notes' => 'Requires urgent review',
            'contact_number' => '919123456789',
        ]);
    }

    public function test_hospital_can_manage_own_doctors(): void
    {
        [$hospitalUser, $hospital] = $this->createApprovedHospital();

        $createResponse = $this->actingAs($hospitalUser)->post(route('hospital.doctors.add'), [
            'name' => 'Arjun Rao',
            'specialization' => 'Transplant Surgery',
            'phone' => '+91 90000 11111',
        ]);

        $createResponse->assertSessionHas('success');
        $doctor = Doctor::where('hospital_id', $hospital->id)->firstOrFail();
        $this->assertSame('919000011111', $doctor->phone);

        $updateResponse = $this->actingAs($hospitalUser)->patch(route('hospital.doctors.update', $doctor), [
            'name' => 'Arjun Kumar',
            'specialization' => 'Renal Surgery',
            'phone' => '+91 90000 22222',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'name' => 'Arjun Kumar',
            'specialization' => 'Renal Surgery',
            'phone' => '919000022222',
        ]);

        $deleteResponse = $this->actingAs($hospitalUser)->delete(route('hospital.doctors.delete', $doctor));

        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('doctors', ['id' => $doctor->id]);
    }

    public function test_planner_assigns_only_a_doctor_from_the_same_hospital(): void
    {
        [$hospitalUser, $hospital] = $this->createApprovedHospital();
        [, $otherHospital] = $this->createApprovedHospital('Other Hospital');
        $doctor = Doctor::create([
            'hospital_id' => $hospital->id,
            'name' => 'Maya Thomas',
            'specialization' => 'Cardiac Surgery',
        ]);
        $otherDoctor = Doctor::create([
            'hospital_id' => $otherHospital->id,
            'name' => 'Nikhil Das',
            'specialization' => 'Renal Surgery',
        ]);
        $transplant = $this->createTransplantForHospital($hospital);

        $validResponse = $this->actingAs($hospitalUser)->post(route('hospital.transplants.slot', $transplant), [
            'slot_date' => '2026-06-12',
            'slot_period' => 'Morning',
            'operating_room' => 'OR-2',
            'doctor_id' => $doctor->id,
        ]);

        $validResponse->assertSessionHas('success');
        $this->assertDatabaseHas('transplants', [
            'id' => $transplant->id,
            'doctor_id' => $doctor->id,
            'surgeon_name' => 'Maya Thomas',
            'operating_room' => 'OR-2',
        ]);

        $invalidResponse = $this->actingAs($hospitalUser)
            ->from(route('hospital.planner'))
            ->post(route('hospital.transplants.slot', $transplant), [
                'slot_date' => '2026-06-13',
                'slot_period' => 'Afternoon',
                'operating_room' => 'OR-3',
                'doctor_id' => $otherDoctor->id,
            ]);

        $invalidResponse->assertSessionHasErrors('doctor_id');
        $this->assertDatabaseMissing('transplants', [
            'id' => $transplant->id,
            'doctor_id' => $otherDoctor->id,
        ]);
    }

    public function test_admin_can_view_doctors_and_organ_inventory_with_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [, $hospital] = $this->createApprovedHospital('Kerala Care Hospital');
        Doctor::create([
            'hospital_id' => $hospital->id,
            'name' => 'Sara Mathew',
            'specialization' => 'Hepatology',
        ]);
        OrganInventory::create([
            'hospital_id' => $hospital->id,
            'organ_type' => 'Liver',
            'units' => 3,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.doctors', ['hospital_id' => $hospital->id, 'specialization' => 'Hepatology']))
            ->assertOk()
            ->assertSee('Sara Mathew')
            ->assertSee('Kerala Care Hospital');

        $this->actingAs($admin)
            ->get(route('admin.organs', ['hospital_id' => $hospital->id, 'organ_type' => 'Liver']))
            ->assertOk()
            ->assertSee('Liver')
            ->assertSee('Kerala Care Hospital')
            ->assertSee('3');
    }

    private function createApprovedHospital(string $name = 'Kerala Trust Hospital'): array
    {
        $user = User::factory()->create(['role' => 'hospital']);
        $hospital = Hospital::create([
            'user_id' => $user->id,
            'name' => $name,
            'location' => 'Kerala',
            'approved' => true,
            'identity_verified' => true,
        ]);

        return [$user, $hospital];
    }

    private function createTransplantForHospital(Hospital $hospital): Transplant
    {
        $donorUser = User::factory()->create(['role' => 'donor']);
        $recipientUser = User::factory()->create(['role' => 'recipient']);
        $donor = Donor::create([
            'user_id' => $donorUser->id,
            'blood_group' => 'O+',
            'organ_type' => 'Kidney',
            'medical_status' => 'VERIFIED',
            'approved' => true,
            'identity_verified' => true,
        ]);
        $recipient = Recipient::create([
            'user_id' => $recipientUser->id,
            'blood_group' => 'A+',
            'organ_needed' => 'Kidney',
            'urgency_level' => 'high',
            'status' => 'MATCHED',
            'admin_approved' => true,
        ]);
        $match = AllocationMatch::create([
            'donor_id' => $donor->id,
            'recipient_id' => $recipient->id,
            'score' => 91,
            'match_score' => 91,
            'status' => 'APPROVED',
        ]);

        return Transplant::create([
            'match_id' => $match->id,
            'hospital_id' => $hospital->id,
            'status' => 'APPROVED',
        ]);
    }
}
