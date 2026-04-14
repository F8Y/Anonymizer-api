<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class AnonymizeRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'full_name is required')]
        #[Assert\Type(type: 'string', message: 'full_name must be a string')]
        public string $fullName,

        #[Assert\NotBlank(message: 'email is required')]
        #[Assert\Email(message: 'email must be valid')]
        public string $email,

        #[Assert\NotBlank(message: 'phone is required')]
        #[Assert\Type(type: 'string', message: 'phone must be a string')]
        #[Assert\Regex(
            pattern: '/^\+?[1-9]\d{10,14}$/',
            message: 'phone must be in international format'
        )]
        public string $phone,

        #[Assert\NotBlank(message: 'birth_date is required')]
        #[Assert\Date(message: 'birth_date must be in YYYY-MM-DD format')]
        public string $birthDate,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            fullName: (string) ($data['full_name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            birthDate: (string) ($data['birth_date'] ?? ''),
        );
    }
}