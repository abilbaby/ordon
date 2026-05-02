<?php

namespace App\DTO;

final class CreateRecipientInviteData
{
    public function __construct(
        public readonly string $recipientName,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $bloodGroup,
        public readonly ?string $notes,
        public readonly ?string $dateOfBirth = null,
        public readonly ?string $gender = null,
        public readonly ?string $organNeeded = null,
        public readonly ?string $urgencyLevel = null,
        public readonly ?int $waitingTime = null,
        public readonly ?string $otherOrgansNeeded = null,
        public readonly ?string $medicalNotes = null,
        public readonly ?string $contactNumber = null,
    ) {
    }
}
