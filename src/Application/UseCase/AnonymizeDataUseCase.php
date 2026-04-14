<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Domain\Anonymization\DTO\AnonymizeResponseDto;
use App\Domain\Anonymization\Service\Anonymizer;

final readonly class AnonymizeDataUseCase
{
    public function __construct(
        private Anonymizer $anonymizer
    ) {
    }

    public function execute(AnonymizeRequestDto $input): AnonymizeResponseDto
    {
        return $this->anonymizer->anonymize($input);
    }
}