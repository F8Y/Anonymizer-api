<?php

declare(strict_types=1);

use App\Application\UseCase\AnonymizeDataUseCase;
use App\Domain\Anonymization\Rule\BirthDateRule;
use App\Domain\Anonymization\Rule\EmailRule;
use App\Domain\Anonymization\Rule\FirstMiddleNameRule;
use App\Domain\Anonymization\Rule\LastNameRule;
use App\Domain\Anonymization\Rule\LoginRule;
use App\Domain\Anonymization\Rule\PhoneRule;
use App\Domain\Anonymization\Rule\PublicIdRule;
use App\Domain\Anonymization\Service\Anonymizer;
use App\Http\Action\AnonymizeAction;
use App\Http\Middleware\ErrorHandlerMiddleware;
use App\Http\Middleware\JsonBodyMiddleware;
use App\Http\Response\JsonResponseFactory;
use App\Http\Response\ProblemDetailsResponseFactory;
use App\Infrastructure\Validation\DtoValidator;
use App\Infrastructure\Validation\ValidationFieldNameMapper;
use function DI\autowire;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return [
    ValidatorInterface::class => static function (): ValidatorInterface {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    },

    PublicIdRule::class => static function (): PublicIdRule {
        $secret = $_ENV['APP_ANONYMIZATION_SECRET']
            ?? getenv('APP_ANONYMIZATION_SECRET')
            ?: 'dev-anonymization-secret';

        return new PublicIdRule($secret);
    },

    LoginRule::class => static function (): LoginRule {
        $secret = $_ENV['APP_ANONYMIZATION_SECRET']
            ?? getenv('APP_ANONYMIZATION_SECRET')
            ?: 'dev-anonymization-secret';

        return new LoginRule($secret);
    },

    FirstMiddleNameRule::class => autowire(),
    LastNameRule::class => autowire(),
    EmailRule::class => autowire(),
    PhoneRule::class => autowire(),
    BirthDateRule::class => autowire(),

    ValidationFieldNameMapper::class => autowire(),
    DtoValidator::class => autowire(),

    JsonResponseFactory::class => autowire(),
    ProblemDetailsResponseFactory::class => autowire(),

    JsonBodyMiddleware::class => autowire(),
    ErrorHandlerMiddleware::class => autowire(),

    Anonymizer::class => autowire(),
    AnonymizeDataUseCase::class => autowire(),
    AnonymizeAction::class => autowire(),
];