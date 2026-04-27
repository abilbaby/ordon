<?php

namespace App\DTO;

use App\Enums\DonationType;

final class DonorDonationPreferencesData
{
    /**
     * @param  array<int, string>  $organs
     */
    public function __construct(
        public readonly DonationType $donationType,
        public readonly array $organs,
        public readonly bool $isAvailable,
        public readonly ?string $notes,
    ) {
    }
}
