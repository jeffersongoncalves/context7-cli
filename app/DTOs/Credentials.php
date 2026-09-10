<?php

namespace App\DTOs;

use JeffersonGoncalves\LaravelZero\Credentials\CredentialsContract;

final class Credentials implements CredentialsContract
{
    public function __construct(
        public readonly string $apiKey,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            apiKey: $data['api_key'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'api_key' => $this->apiKey,
        ];
    }

    public function isValid(): bool
    {
        return $this->apiKey !== '';
    }
}
