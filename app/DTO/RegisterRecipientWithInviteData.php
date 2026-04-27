<?php

namespace App\DTO;

final class RegisterRecipientWithInviteData
{
    public function __construct(
        public readonly string $rvid,
        public readonly string $identityType,
        public readonly string $identityNumber,
        public readonly string $password,
    ) {
    }
}
