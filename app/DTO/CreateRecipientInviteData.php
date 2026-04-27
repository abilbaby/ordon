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
    ) {
    }
}
