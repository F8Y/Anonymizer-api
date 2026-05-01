<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Contract\AnonymizationRuleInterface;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;

final readonly class PublicIdRule implements AnonymizationRuleInterface
{
    public function __construct(
        private string $secret
    ) {
    }

    public function apply(AnonymizeRequestDto $input): string
    {
        $source = implode('|', [
            $this->normalize($input->login),
            $this->normalize($input->lastName),
            $this->normalize($input->firstMiddleName),
            $input->birthDate ?? '',
            mb_strtolower(trim($input->email)),
        ]);

        $hash = hash_hmac('sha256', $source, $this->secret);

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