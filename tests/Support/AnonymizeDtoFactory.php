<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;

final class AnonymizeDtoFactory
{
    /**
     * @param array<string, mixed> $overrides
     */
    public static function make(array $overrides = []): AnonymizeRequestDto
    {
        return new AnonymizeRequestDto(
            login: (string) ($overrides['login'] ?? 'ivanov_ii'),
            firstMiddleName: (string) ($overrides['firstMiddleName'] ?? 'Иван Иванович'),
            lastName: (string) ($overrides['lastName'] ?? 'Иванов'),
            email: (string) ($overrides['email'] ?? 'ivanov@example.com'),
            phone: array_key_exists('phone', $overrides) ? $overrides['phone'] : '+79991234567',
            birthDate: array_key_exists('birthDate', $overrides) ? $overrides['birthDate'] : '2010-04-12',
        );
    }
}