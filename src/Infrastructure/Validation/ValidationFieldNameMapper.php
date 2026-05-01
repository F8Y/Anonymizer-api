<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

final class ValidationFieldNameMapper
{
    private const MAP = [
        'login' => 'login',
        'firstMiddleName' => 'first_middle_name',
        'lastName' => 'last_name',
        'email' => 'email',
        'phone' => 'phone',
        'birthDate' => 'birth_date',
    ];

    public function toApiField(string $propertyPath): string
    {
        return self::MAP[$propertyPath] ?? $propertyPath;
    }
}