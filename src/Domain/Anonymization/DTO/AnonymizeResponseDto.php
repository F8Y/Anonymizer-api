<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\DTO;

final readonly class AnonymizeResponseDto
{
    public function __construct(
        public string $fullName,
        public string $email,
        public string $phone,
        public string $birthDate,
    ) {
    }

    public function toArray(): array
    {
        return [
            'full_name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birthDate,
        ];
    }
}