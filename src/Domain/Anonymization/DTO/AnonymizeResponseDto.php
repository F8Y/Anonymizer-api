<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\DTO;

final readonly class AnonymizeResponseDto
{
    public function __construct(
        public string $publicId,
        public string $login,
        public string $firstMiddleName,
        public string $lastName,
        public string $email,
        public ?string $phone,
        public ?string $birthDate,
    ) {
    }

    public function toArray(): array
    {
        return [
            'public_id' => $this->publicId,
            'login' => $this->login,
            'first_middle_name' => $this->firstMiddleName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birthDate,
        ];
    }
}