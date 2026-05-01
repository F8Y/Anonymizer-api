<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class AnonymizeRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'login is required')]
        #[Assert\Type(type: 'string', message: 'login must be a string')]
        public string $login,

        #[Assert\NotBlank(message: 'first_middle_name is required')]
        #[Assert\Type(type: 'string', message: 'first_middle_name must be a string')]
        public string $firstMiddleName,

        #[Assert\NotBlank(message: 'last_name is required')]
        #[Assert\Type(type: 'string', message: 'last_name must be a string')]
        public string $lastName,

        #[Assert\NotBlank(message: 'email is required')]
        #[Assert\Email(message: 'email must be valid')]
        public string $email,

        #[Assert\Regex(
            pattern: '/^\+?[1-9]\d{10,14}$/',
            message: 'phone must be in international format'
        )]
        public ?string $phone = null,

        #[Assert\Date(message: 'birth_date must be in YYYY-MM-DD format')]
        public ?string $birthDate = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            login: self::requiredString($data, 'login'),
            firstMiddleName: self::requiredString($data, 'first_middle_name'),
            lastName: self::requiredString($data, 'last_name'),
            email: self::requiredString($data, 'email'),
            phone: self::nullableString($data['phone'] ?? null),
            birthDate: self::nullableString($data['birth_date'] ?? null),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function requiredString(array $data, string $key): string
    {
        return trim((string) ($data[$key] ?? ''));
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public function fullName(): string
    {
        return trim($this->lastName . ' ' . $this->firstMiddleName);
    }
}