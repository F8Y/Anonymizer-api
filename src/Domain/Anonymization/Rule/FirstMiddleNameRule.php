<?php

declare(strict_types=1);

namespace App\Domain\Anonymization\Rule;

use App\Domain\Anonymization\Contract\AnonymizationRuleInterface;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;

final readonly class FirstMiddleNameRule implements AnonymizationRuleInterface
{
    public function apply(AnonymizeRequestDto $input): string
    {
        return '[обезличено]';
    }
}