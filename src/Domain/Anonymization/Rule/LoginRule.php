<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Contract\AnonymizationRuleInterface;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;

final readonly class LoginRule implements AnonymizationRuleInterface
{
    public function __construct(
        private string $secret
    ) {
    }

    public function apply(AnonymizeRequestDto $input): string
    {
        $normalized = mb_strtolower(trim($input->login));
        $hash = hash_hmac('sha256', $normalized, $this->secret);

        return 'LOGIN-' . strtoupper(substr($hash, 0, 12));
    }
}