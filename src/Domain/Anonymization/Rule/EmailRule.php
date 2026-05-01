<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Contract\AnonymizationRuleInterface;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;

final readonly class EmailRule implements AnonymizationRuleInterface
{
    public function apply(AnonymizeRequestDto $input): string
    {
        [$localPart, $domain] = explode('@', $input->email, 2);

        $localPart = mb_strtolower(trim($localPart));
        $domain = mb_strtolower(trim($domain));

        return $this->maskLocalPart($localPart) . '@' . $this->maskDomain($domain);
    }

    private function maskLocalPart(string $localPart): string
    {
        $length = mb_strlen($localPart);

        if ($length <= 1) {
            return '*';
        }

        if ($length === 2) {
            return mb_substr($localPart, 0, 1) . '*';
        }

        return mb_substr($localPart, 0, 1)
            . str_repeat('*', max(3, $length - 2))
            . mb_substr($localPart, -1);
    }

    private function maskDomain(string $domain): string
    {
        $parts = explode('.', $domain);

        if (count($parts) < 2) {
            return '***';
        }

        $tld = array_pop($parts);

        return '***.' . $tld;
    }
}