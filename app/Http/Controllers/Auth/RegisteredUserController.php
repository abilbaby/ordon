<?php

namespace App\Http\Controllers\Auth;

use App\Enums\DonationType;
use App\Enums\OrganType;
use App\DTO\RegisterRecipientWithInviteData;
use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\DonorOrgan;
use App\Models\Hospital;
use App\Models\User;
use App\Services\RecipientInvitationService;
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
    public function store(Request $request, RecipientInvitationService $recipientInvitationService): RedirectResponse
    {
        if ($request->input('role') === 'recipient') {
            $request->merge([
                'identity_type' => $request->filled('identity_type') ? strtolower((string) $request->input('identity_type')) : null,
                'identity_number' => $request->filled('identity_number')
                    ? strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', trim((string) $request->input('identity_number'))))
                    : null,
                'rvid' => $request->filled('rvid') ? strtoupper(trim((string) $request->input('rvid'))) : null,
            ]);
        }

        $validated = $request->validate([
            'name' => ['required_unless:role,recipient', 'nullable', 'string', 'max:255'],
            'email' => ['required_unless:role,recipient', 'nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'in:donor,recipient,hospital'],
            'rvid' => ['required_if:role,recipient', 'nullable', 'string'],
            'identity_type' => ['required_if:role,donor,recipient', 'nullable', 'in:aadhaar,passport,voter_id,driving_licence,pan,other'],
            'identity_number' => [
                'required_if:role,donor,recipient',
                'nullable',
                'string',
                'max:60',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    if ($request->input('role') !== 'recipient' || ! $value) {
                        return;
                    }

                    $number = (string) $value;

                    match ($request->input('identity_type')) {
                        'aadhaar' => preg_match('/^\d{12}$/', $number) === 1
                            ?: $fail('Aadhaar number must be exactly 12 digits.'),
                        'pan' => preg_match('/^[A-Z]{5}\d{4}[A-Z]$/', $number) === 1
                            ?: $fail('PAN must follow the format ABCDE1234F.'),
                        'passport' => preg_match('/^[A-Z0-9]{6,9}$/', $number) === 1
                            ?: $fail('Passport number must be 6 to 9 alphanumeric characters.'),
                        'driving_licence' => preg_match('/^[A-Z0-9]{10,16}$/', $number) === 1
                            ?: $fail('Driving licence number must be 10 to 16 alphanumeric characters.'),
                        'voter_id' => preg_match('/^[A-Z0-9]{10}$/', $number) === 1
                            ?: $fail('Voter ID must be exactly 10 alphanumeric characters.'),
                        'other' => preg_match('/^[A-Z0-9]{5,20}$/', $number) === 1
                            ?: $fail('ID number must be 5 to 20 alphanumeric characters.'),
                        default => $fail('Select a valid government ID type first.'),
                    };
                },
            ],
            'hospital_name' => ['required_if:role,hospital', 'nullable', 'string', 'max:255'],
            'hospital_registration_id' => ['required_if:role,hospital', 'nullable', 'string', 'max:60'],
            'hospital_location' => ['required_if:role,hospital', 'nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validated['role'] === 'recipient') {
            $user = $recipientInvitationService->registerWithInvite(
                new RegisterRecipientWithInviteData(
                    $validated['rvid'],
                    $validated['identity_type'],
                    $validated['identity_number'],
                    $validated['password'],
                )
            );

            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('recipient.dashboard')->with(
                'success',
                'Registration complete. Awaiting hospital/admin approval for full recipient access.'
            );
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
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
                    'identity_type' => $validated['identity_type'],
                    'identity_number' => $validated['identity_number'],
                    'identity_verified' => false,
                ]
            );

            DonorOrgan::firstOrCreate(
                ['donor_id' => $donor->id, 'organ_type' => OrganType::Kidney->value]
            );
        }

        if ($user->role === 'hospital') {
            Hospital::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $validated['hospital_name'],
                    'location' => $validated['hospital_location'],
                    'approved' => false,
                    'identity_type' => 'hospital_registration_id',
                    'identity_number' => $validated['hospital_registration_id'],
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
