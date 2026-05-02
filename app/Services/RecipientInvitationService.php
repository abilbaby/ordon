<?php

namespace App\Services;

use App\DTO\CreateRecipientInviteData;
use App\DTO\RegisterRecipientWithInviteData;
use App\Mail\RecipientInvitationMail;
use App\Models\Hospital;
use App\Models\Recipient;
use App\Models\RecipientVerification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class RecipientInvitationService
{
    public function __construct(
        private readonly IdentityNumberService $identityNumberService,
    ) {
    }

    public function createRecipientInvite(Hospital $hospital, CreateRecipientInviteData $data): RecipientVerification
    {
        $rvid = $this->generateRvid();
        $expiresAt = now()->addHours(24);
        $link = route('recipient.invite.register', ['rvid' => $rvid]);

        $invite = RecipientVerification::create([
            'rvid' => $rvid,
            'hospital_id' => $hospital->id,
            'recipient_name' => $data->recipientName,
            'email' => $data->email,
            'phone' => $data->phone,
            'blood_group' => $data->bloodGroup,
            'notes' => $data->notes,
            'registration_link' => $link,
            'status' => 'Pending',
            'expires_at' => $expiresAt,
            'date_of_birth' => $data->dateOfBirth,
            'gender' => $data->gender,
            'organ_needed' => $data->organNeeded,
            'urgency_level' => $data->urgencyLevel,
            'medical_notes' => $data->medicalNotes,
            'contact_number' => $data->contactNumber,
        ]);

        $mail = new RecipientInvitationMail($invite, $hospital->name);
        try {
            if (config('queue.default') === 'sync') {
                Mail::to($data->email)->send($mail);
            } else {
                Mail::to($data->email)->queue($mail);
            }
        } catch (Throwable) {
            Mail::to($data->email)->send($mail);
        }

        return $invite;
    }

    public function validateRvid(string $rvid): RecipientVerification
    {
        $invite = RecipientVerification::where('rvid', $rvid)->first();
        if (! $invite) {
            throw ValidationException::withMessages(['rvid' => 'Invalid recipient invitation link.']);
        }

        if ($invite->status !== 'Pending') {
            throw ValidationException::withMessages(['rvid' => 'This recipient invitation link has already been used.']);
        }

        if ($invite->expires_at->isPast()) {
            $invite->update(['status' => 'Expired']);
            throw ValidationException::withMessages(['rvid' => 'This recipient invitation link has expired.']);
        }

        return $invite;
    }

    public function registerWithInvite(RegisterRecipientWithInviteData $data): User
    {
        $invite = $this->validateRvid($data->rvid);
        $normalizedIdentityNumber = $this->identityNumberService->normalize($data->identityNumber);
        $identityHash = $this->identityNumberService->hash($normalizedIdentityNumber);

        $duplicate = Recipient::query()
            ->where('identity_number_hash', $identityHash)
            ->exists();
        if ($duplicate) {
            throw ValidationException::withMessages([
                'identity_number' => 'This government ID number is already used by another recipient.',
            ]);
        }

        return DB::transaction(function () use ($invite, $data): User {
            $lockedInvite = RecipientVerification::where('id', $invite->id)->lockForUpdate()->firstOrFail();
            if ($lockedInvite->status !== 'Pending') {
                throw ValidationException::withMessages(['rvid' => 'This recipient invitation link has already been used.']);
            }

            $existing = User::where('email', $invite->email)->first();
            if ($existing) {
                throw ValidationException::withMessages(['email' => 'A user with this invitation email already exists.']);
            }

            $normalizedIdentityNumber = $this->identityNumberService->normalize($data->identityNumber);
            $identityHash = $this->identityNumberService->hash($normalizedIdentityNumber);
            $duplicate = Recipient::query()
                ->where('identity_number_hash', $identityHash)
                ->exists();
            if ($duplicate) {
                throw ValidationException::withMessages([
                    'identity_number' => 'This government ID number is already used by another recipient.',
                ]);
            }

            $user = User::create([
                'name' => $invite->recipient_name,
                'email' => $invite->email,
                'role' => 'recipient',
                'password' => Hash::make($data->password),
            ]);

            Recipient::create([
                'user_id' => $user->id,
                'hospital_id' => $invite->hospital_id,
                'recipient_verification_id' => $invite->id,
                'blood_group' => $invite->blood_group,
                'organ_needed' => $invite->organ_needed ?? 'Kidney',
                'urgency_level' => $invite->urgency_level ?? 'medium',
                'waiting_time' => 0,
                'status' => 'REGISTERED',
                'identity_type' => $data->identityType,
                'identity_number' => $normalizedIdentityNumber,
                'identity_number_hash' => $identityHash,
                'identity_number_last4' => $this->identityNumberService->last4($normalizedIdentityNumber),
                'identity_verified' => false,
                'hospital_verified' => false,
                'admin_approved' => false,
                'flagged_for_review' => false,
                'phone' => $invite->contact_number ?? $invite->phone,
                'date_of_birth' => $invite->date_of_birth,
                'gender' => $invite->gender,
            ]);

            $lockedInvite->update(['status' => 'Used']);

            return $user;
        });
    }

    public function approveRecipientByHospital(Recipient $recipient): void
    {
        $suspicious = empty($recipient->identity_number)
            || empty($recipient->identity_type)
            || ! $recipient->recipientVerification
            || ($recipient->recipientVerification && $recipient->recipientVerification->blood_group !== $recipient->blood_group);

        $recipient->update([
            'hospital_verified' => true,
            'doctor_approved' => true,
            'identity_verified' => true,
            'flagged_for_review' => $suspicious,
            'admin_approved' => ! $suspicious,
            'status' => ! $suspicious ? 'VERIFIED' : $recipient->status,
        ]);
    }

    public function rejectRecipientByHospital(Recipient $recipient): void
    {
        $recipient->update([
            'hospital_verified' => false,
            'admin_approved' => false,
            'status' => 'REJECTED',
        ]);
    }

    private function generateRvid(): string
    {
        do {
            $rvid = 'RVID-'.Str::upper(Str::random(20));
        } while (RecipientVerification::where('rvid', $rvid)->exists());

        return $rvid;
    }
}
