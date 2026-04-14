<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Application\UseCase\AnonymizeDataUseCase;
use App\Domain\Anonymization\DTO\AnonymizeRequestDto;
use App\Infrastructure\Validation\DtoValidator;
use App\Support\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final readonly class AnonymizeAction
{
    public function __construct(
        private DtoValidator $dtoValidator,
        private AnonymizeDataUseCase $useCase
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $rawBody = (string) $request->getBody();
        $payload = json_decode($rawBody, true);

        if (!is_array($payload)) {
            return $this->json(
                $response,
                ['error' => ['code' => 'invalid_json', 'message' => 'Request body must be valid JSON']],
                400
            );
        }

        try {
            $dto = AnonymizeRequestDto::fromArray($payload);
            $this->dtoValidator->validate($dto);

            $result = $this->useCase->execute($dto);

            return $this->json($response, $result->toArray(), 200);
        } catch (ValidationException $e) {
            return $this->json(
                $response,
                [
                    'error' => [
                        'code' => 'validation_error',
                        'message' => 'Request validation failed',
                        'details' => $e->errors(),
                    ],
                ],
                422
            );
        }
    }

    private function json(ResponseInterface $response, array $data, int $status): ResponseInterface
    {
        $response->getBody()->write(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}