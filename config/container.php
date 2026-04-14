<?php

declare(strict_types=1);

use App\Application\UseCase\AnonymizeDataUseCase;
use App\Domain\Anonymization\Service\Anonymizer;
use App\Http\Action\AnonymizeAction;
use App\Infrastructure\Validation\DtoValidator;
use DI\ContainerBuilder;
use function DI\autowire;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return [
    ValidatorInterface::class => static function (): ValidatorInterface {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    },

    DtoValidator::class => autowire(),
    Anonymizer::class => autowire(),
    AnonymizeDataUseCase::class => autowire(),
    AnonymizeAction::class => autowire(),
];