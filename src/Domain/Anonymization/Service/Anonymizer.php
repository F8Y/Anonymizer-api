<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Service;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\DTO\AnonymizeResponseDto;
use App\Domain\Anonymization\Rule\BirthDateRule;
use App\Domain\Anonymization\Rule\EmailRule;
use App\Domain\Anonymization\Rule\FirstMiddleNameRule;
use App\Domain\Anonymization\Rule\LastNameRule;
use App\Domain\Anonymization\Rule\LoginRule;
use App\Domain\Anonymization\Rule\PhoneRule;
use App\Domain\Anonymization\Rule\PublicIdRule;

final readonly class Anonymizer
{
    public function __construct(
        private PublicIdRule $publicIdRule,
        private LoginRule $loginRule,
        private FirstMiddleNameRule $firstMiddleNameRule,
        private LastNameRule $lastNameRule,
        private EmailRule $emailRule,
        private PhoneRule $phoneRule,
        private BirthDateRule $birthDateRule,
    ) {
    }

    public function anonymize(AnonymizeRequestDto $input): AnonymizeResponseDto
    {
        return new AnonymizeResponseDto(
            publicId: $this->publicIdRule->apply($input),
            login: $this->loginRule->apply($input),
            firstMiddleName: $this->firstMiddleNameRule->apply($input),
            lastName: $this->lastNameRule->apply($input),
            email: $this->emailRule->apply($input),
            phone: $this->phoneRule->apply($input),
            birthDate: $this->birthDateRule->apply($input),
        );
    }
}