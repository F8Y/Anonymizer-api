<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Service;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\DTO\AnonymizeResponseDto;
use App\Domain\Anonymization\Rule\BirthDateRule;
use App\Domain\Anonymization\Rule\EmailRule;
use App\Domain\Anonymization\Rule\FullNameRule;
use App\Domain\Anonymization\Rule\PhoneRule;

final readonly class Anonymizer
{
    public function __construct(
        private FullNameRule $fullNameRule,
        private EmailRule $emailRule,
        private PhoneRule $phoneRule,
        private BirthDateRule $birthDateRule,
    ) {
    }

    public function anonymize(AnonymizeRequestDto $input): AnonymizeResponseDto
    {
        return new AnonymizeResponseDto(
            fullName: $this->fullNameRule->apply($input),
            email: $this->emailRule->apply($input),
            phone: $this->phoneRule->apply($input),
            birthDate: $this->birthDateRule->apply($input),
        );
    }
}