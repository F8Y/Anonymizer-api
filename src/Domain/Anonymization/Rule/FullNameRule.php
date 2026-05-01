<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Contract\AnonymizationRuleInterface;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;

final readonly class FullNameRule implements AnonymizationRuleInterface
{
    public function __construct(
        private string $secret
    ) {
    }

    public function apply(AnonymizeRequestDto $input): string
    {
        $normalized = $this->normalize($input->fullName);

        $hash = hash_hmac(
            algo: 'sha256',
            data: $normalized,
            key: $this->secret
        );

        return 'USER-' . strtoupper(substr($hash, 0, 12));
    }

    private function normalize(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
        $value = mb_strtolower($value);
        $value = str_replace('ё', 'е', $value);

        return $value;
    }
}