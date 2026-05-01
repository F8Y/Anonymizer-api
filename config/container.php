<?php

declare(strict_types=1);

use App\Application\UseCase\AnonymizeDataUseCase;
use App\Domain\Anonymization\Rule\BirthDateRule;
use App\Domain\Anonymization\Rule\EmailRule;
use App\Domain\Anonymization\Rule\FullNameRule;
use App\Domain\Anonymization\Rule\PhoneRule;
use App\Domain\Anonymization\Service\Anonymizer;
use App\Http\Action\AnonymizeAction;
use App\Infrastructure\Validation\DtoValidator;
use function DI\autowire;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return [
    ValidatorInterface::class => static function (): ValidatorInterface {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    },

    FullNameRule::class => static function (): FullNameRule {
        $secret = $_ENV['APP_ANONYMIZATION_SECRET']
            ?? getenv('APP_ANONYMIZATION_SECRET')
            ?: 'dev-anonymization-secret';

        return new FullNameRule($secret);
    },

    EmailRule::class => autowire(),
    PhoneRule::class => autowire(),
    BirthDateRule::class => autowire(),

    DtoValidator::class => autowire(),
    Anonymizer::class => autowire(),
    AnonymizeDataUseCase::class => autowire(),
    AnonymizeAction::class => autowire(),
];