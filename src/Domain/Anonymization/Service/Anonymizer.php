<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Service;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\DTO\AnonymizeResponseDto;
use DateTimeImmutable;

final class Anonymizer
{
    public function anonymize(AnonymizeRequestDto $input): AnonymizeResponseDto
    {
        return new AnonymizeResponseDto(
            fullName: $this->anonymizeFullName($input->fullName),
            email: $this->maskEmail($input->email),
            phone: $this->maskPhone($input->phone),
            birthDate: $this->extractYear($input->birthDate),
        );
    }

    private function anonymizeFullName(string $fullName): string
    {
        $normalized = mb_strtolower(trim($fullName));
        $hash = strtoupper(substr(hash('sha256', $normalized), 0, 10));

        return 'USER-' . $hash;
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);

        $first = mb_substr($local, 0, 1);
        return sprintf('%s***@%s', $first, $domain);
    }

    private function maskPhone(string $phone): string
    {
        $normalized = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($normalized) < 4) {
            return '***';
        }

        $lastTwo = substr($normalized, -2);
        return '+7*******' . $lastTwo;
    }

    private function extractYear(string $birthDate): string
    {
        $date = new DateTimeImmutable($birthDate);

        return $date->format('Y');
    }
}