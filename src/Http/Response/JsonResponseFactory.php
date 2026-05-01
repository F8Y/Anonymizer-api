<?php

declare(strict_types=1);

namespace App\Http\Response;

use Psr\Http\Message\ResponseInterface;

final class JsonResponseFactory
{
    /**
     * @param array<string, mixed> $payload
     */
    public function create(
        ResponseInterface $response,
        array $payload,
        int $status = 200
    ): ResponseInterface {
        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($status);
    }
}