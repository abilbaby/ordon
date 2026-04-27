<?php

namespace App\Services;

class IdentityNumberService
{
    public function normalize(string $identityNumber): string
    {
        return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', trim($identityNumber)));
    }

    public function hash(string $normalized): string
    {
        return hash('sha256', $normalized);
    }

    public function last4(string $normalized): string
    {
        return substr($normalized, -4);
    }

    public function maskLast4(?string $last4): string
    {
        if (! $last4) {
            return 'N/A';
        }

        return '****'.$last4;
    }
}
