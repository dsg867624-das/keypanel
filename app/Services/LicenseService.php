<?php

namespace App\Services;

use App\Models\License;
use Illuminate\Support\Str;

class LicenseService
{
    public function generateKey(): string
    {
        return 'KP-' .
            strtoupper(Str::random(8)) . '-' .
            strtoupper(Str::random(8)) . '-' .
            strtoupper(Str::random(8));
    }

    public function hashKey(string $key): string
    {
        return hash('sha256', $key);
    }

    public function createLicense(
        ?string $name = null,
        ?\DateTimeInterface $expiresAt = null,
        int $activationLimit = 1
    ): array {
        $key = $this->generateKey();

        License::create([
            'key_hash' => $this->hashKey($key),
            'name' => $name,
            'status' => 'active',
            'expires_at' => $expiresAt,
            'activation_limit' => $activationLimit,
            'activation_count' => 0,
        ]);

        return [
            'key' => $key,
        ];
    }
}

