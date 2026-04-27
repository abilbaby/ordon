<?php

namespace App\Http\Controllers\Auth;

use App\Enums\DonationType;
use App\Enums\OrganType;
use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\DonorOrgan;
use App\Models\Hospital;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'in:donor,recipient,hospital'],
            'identity_type' => ['required', 'in:aadhaar,passport,voter_id,driving_licence,pan,other'],
            'identity_number' => ['required', 'string', 'max:60'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        if ($user->role === 'donor') {
            $donor = Donor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'blood_group' => 'O+',
                    'organ_type' => OrganType::Kidney->value,
                    'medical_status' => 'REGISTERED',
                    'is_available' => false,
                    'donation_type' => DonationType::LivingDonation->value,
                    'approved' => false,
                    'identity_type' => $request->string('identity_type')->toString(),
                    'identity_number' => $request->string('identity_number')->toString(),
                    'identity_verified' => false,
                ]
            );

            DonorOrgan::firstOrCreate(
                ['donor_id' => $donor->id, 'organ_type' => OrganType::Kidney->value]
            );
        }

        if ($user->role === 'recipient') {
            Recipient::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'blood_group' => 'A+',
                    'organ_needed' => OrganType::Kidney->value,
                    'urgency_level' => 'medium',
                    'waiting_time' => 0,
                    'status' => 'REGISTERED',
                    'hospital_verified' => false,
                    'admin_approved' => false,
                    'flagged_for_review' => false,
                    'identity_type' => $request->string('identity_type')->toString(),
                    'identity_number' => $request->string('identity_number')->toString(),
                    'identity_verified' => false,
                ]
            );
        }

        if ($user->role === 'hospital') {
            Hospital::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name.' Hospital',
                    'location' => 'Pending',
                    'approved' => false,
                    'identity_type' => $request->string('identity_type')->toString(),
                    'identity_number' => $request->string('identity_number')->toString(),
                    'identity_verified' => false,
                ]
            );
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(match ($user->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'donor' => route('donor.dashboard', absolute: false),
            'recipient' => route('recipient.dashboard', absolute: false),
            'hospital' => route('hospital.dashboard', absolute: false),
            default => route('profile.edit', absolute: false),
        });
    }
}
