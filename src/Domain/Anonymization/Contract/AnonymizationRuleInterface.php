<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Contract;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;

interface AnonymizationRuleInterface
{
    public function apply(AnonymizeRequestDto $input): string;
}