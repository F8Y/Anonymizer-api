<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

use App\Support\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class DtoValidator
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function validate(object $dto): void
    {
        $violations = $this->validator->validate($dto);

        if (count($violations) === 0) {
            return;
        }

        $errors = [];

        foreach ($violations as $violation) {
            $field = $violation->getPropertyPath();
            $errors[$field][] = $violation->getMessage();
        }

        throw new ValidationException($errors);
    }
}