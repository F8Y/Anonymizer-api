<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Application\UseCase\AnonymizeDataUseCase;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Http\Response\JsonResponseFactory;
use App\Infrastructure\Validation\DtoValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AnonymizeAction
{
    public function __construct(
        private DtoValidator $dtoValidator,
        private AnonymizeDataUseCase $useCase,
        private JsonResponseFactory $jsonResponseFactory,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $payload = $request->getParsedBody();

        if (!is_array($payload)) {
            $payload = [];
        }

        $dto = AnonymizeRequestDto::fromArray($payload);

        $this->dtoValidator->validate($dto);

        $result = $this->useCase->execute($dto);

        return $this->jsonResponseFactory->create(
            response: $response,
            payload: $result->toArray()
        );
    }
}