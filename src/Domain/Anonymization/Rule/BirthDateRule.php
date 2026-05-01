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
        $birthDate = trim($input->birthDate);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthDate)) {
            throw new InvalidArgumentException('birth_date must be in YYYY-MM-DD format');
        }

        [$year, $month, $day] = array_map('intval', explode('-', $birthDate));

        if (!checkdate($month, $day, $year)) {
            throw new InvalidArgumentException('birth_date must be a valid calendar date');
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $birthDate);

        if ($date === false) {
            throw new InvalidArgumentException('birth_date must be a valid date');
        }

        return $date->format('Y');
    }
}