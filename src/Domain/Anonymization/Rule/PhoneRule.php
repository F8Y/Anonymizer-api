<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Contract\AnonymizationRuleInterface;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;

final readonly class PhoneRule implements AnonymizationRuleInterface
{
    public function apply(AnonymizeRequestDto $input): ?string
    {
        if ($input->phone === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $input->phone) ?? '';

        if ($digits === '') {
            return null;
        }

        $normalized = $this->normalizeRussianPhone($digits);

        return $this->mask($normalized);
    }

    private function normalizeRussianPhone(string $digits): string
    {
        if (strlen($digits) === 11 && str_starts_with($digits, '8')) {
            return '7' . substr($digits, 1);
        }

        return $digits;
    }

    private function mask(string $digits): string
    {
        $length = strlen($digits);

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        $countryCode = $this->detectCountryCode($digits);
        $lastTwo = substr($digits, -2);
        $maskedLength = max(4, $length - strlen($countryCode) - 2);

        return '+' . $countryCode . str_repeat('*', $maskedLength) . $lastTwo;
    }

    private function detectCountryCode(string $digits): string
    {
        if (str_starts_with($digits, '7')) {
            return '7';
        }

        if (str_starts_with($digits, '1')) {
            return '1';
        }

        return substr($digits, 0, min(3, strlen($digits)));
    }
}