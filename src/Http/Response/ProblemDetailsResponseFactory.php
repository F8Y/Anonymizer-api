<?php

declare(strict_types=1);

namespace App\Http\Response;

use App\Support\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

final class ProblemDetailsResponseFactory
{
    public function invalidJson(): ResponseInterface
    {
        return $this->create([
            'type' => 'https://sirius27.local/problems/invalid-json',
            'title' => 'Invalid JSON',
            'status' => 400,
            'detail' => 'Request body must be valid JSON',
        ], 400);
    }

    public function validation(ValidationException $exception): ResponseInterface
    {
        return $this->create([
            'type' => 'https://sirius27.local/problems/validation-error',
            'title' => 'Validation error',
            'status' => 422,
            'detail' => 'Request validation failed',
            'errors' => $exception->errors(),
        ], 422);
    }

    public function internalServerError(): ResponseInterface
    {
        return $this->create([
            'type' => 'https://sirius27.local/problems/internal-server-error',
            'title' => 'Internal server error',
            'status' => 500,
            'detail' => 'Unexpected server error',
        ], 500);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function create(array $payload, int $status): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withHeader('Content-Type', 'application/problem+json; charset=utf-8')
            ->withStatus($status);
    }
}