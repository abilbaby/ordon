<?php

namespace Database\Seeders;

use App\Enums\DonationType;
use App\Enums\OrganType;
use App\Models\AllocationMatch;
use App\Models\Donor;
use App\Models\DonorOrgan;
use App\Models\Hospital;
use App\Models\Recipient;
use App\Models\Transplant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ordon.test'],
            [
                'name' => 'System Admin',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        $donorUser = User::updateOrCreate(
            ['email' => 'donor@ordon.test'],
            [
                'name' => 'Rahim Donor',
                'role' => 'donor',
                'password' => Hash::make('password123'),
            ]
        );

        $recipientUser = User::updateOrCreate(
            ['email' => 'recipient@ordon.test'],
            [
                'name' => 'Karim Recipient',
                'role' => 'recipient',
                'password' => Hash::make('password123'),
            ]
        );

        $hospitalUser = User::updateOrCreate(
            ['email' => 'hospital@ordon.test'],
            [
                'name' => 'ORDON Hospital',
                'role' => 'hospital',
                'password' => Hash::make('password123'),
            ]
        );

        $donor = Donor::updateOrCreate(
            ['user_id' => $donorUser->id],
            [
                'blood_group' => 'O+',
                'organ_type' => OrganType::Kidney->value,
                'medical_status' => 'MATCHED',
                'is_available' => true,
                'donation_type' => DonationType::LivingDonation->value,
                'approved' => true,
                'identity_type' => 'national_id',
                'identity_number' => 'DONOR-DEMO-001',
                'identity_verified' => true,
                'available_until' => now()->addDays(30),
            ]
        );
        DonorOrgan::updateOrCreate(['donor_id' => $donor->id, 'organ_type' => OrganType::Kidney->value], []);
        DonorOrgan::updateOrCreate(['donor_id' => $donor->id, 'organ_type' => OrganType::Liver->value], []);

        $recipient = Recipient::updateOrCreate(
            ['user_id' => $recipientUser->id],
            [
                'blood_group' => 'A+',
                'organ_needed' => OrganType::Kidney->value,
                'urgency_level' => 'high',
                'waiting_time' => 120,
                'status' => 'MATCHED',
                'identity_type' => 'passport',
                'identity_number' => 'RECIPIENT-DEMO-001',
                'identity_verified' => true,
            ]
        );

        $hospital = Hospital::updateOrCreate(
            ['user_id' => $hospitalUser->id],
            [
                'name' => 'ORDON Medical Center',
                'location' => 'Dhaka',
                'approved' => true,
                'identity_type' => 'national_id',
                'identity_number' => 'HOSPITAL-DEMO-001',
                'identity_verified' => true,
            ]
        );

        $match = AllocationMatch::updateOrCreate(
            [
                'donor_id' => $donor->id,
                'recipient_id' => $recipient->id,
            ],
            [
                'score' => 150,
                'status' => 'APPROVED',
            ]
        );

        Transplant::updateOrCreate(
            ['match_id' => $match->id],
            [
                'hospital_id' => $hospital->id,
                'status' => 'APPROVED',
                'scheduled_at' => now()->addDays(2),
            ]
        );

        $recipientTwoUser = User::updateOrCreate(
            ['email' => 'recipient2@ordon.test'],
            [
                'name' => 'Nila Recipient',
                'role' => 'recipient',
                'password' => Hash::make('password123'),
            ]
        );

        Recipient::updateOrCreate(
            ['user_id' => $recipientTwoUser->id],
            [
                'blood_group' => 'B+',
                'organ_needed' => OrganType::Liver->value,
                'urgency_level' => 'medium',
                'waiting_time' => 60,
                'status' => 'VERIFIED',
                'identity_type' => 'aadhaar',
                'identity_number' => 'RECIPIENT-DEMO-002',
                'identity_verified' => true,
            ]
        );

        $recipientThreeUser = User::updateOrCreate(
            ['email' => 'recipient3@ordon.test'],
            [
                'name' => 'Sadia Recipient',
                'role' => 'recipient',
                'password' => Hash::make('password123'),
            ]
        );

        Recipient::updateOrCreate(
            ['user_id' => $recipientThreeUser->id],
            [
                'blood_group' => 'AB+',
                'organ_needed' => OrganType::Kidney->value,
                'urgency_level' => 'low',
                'waiting_time' => 20,
                'status' => 'REGISTERED',
                'identity_type' => 'passport',
                'identity_number' => 'RECIPIENT-DEMO-003',
                'identity_verified' => false,
            ]
        );

        $this->command?->info('Demo users created: admin/donor/recipient/hospital');
    }
}
