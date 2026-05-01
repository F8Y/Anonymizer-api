<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Contract\AnonymizationRuleInterface;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class BirthDateRule implements AnonymizationRuleInterface
{
    public function apply(AnonymizeRequestDto $input): string
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $input->birthDate);

        if ($date === false) {
            throw new InvalidArgumentException('Invalid birth date format');
        }

        return $date->format('Y');
    }
}