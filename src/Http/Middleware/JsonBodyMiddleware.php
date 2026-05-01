<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Exception\InvalidJsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JsonBodyMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (!$this->shouldParseJson($request)) {
            return $handler->handle($request);
        }

        $rawBody = trim((string) $request->getBody());

        if ($rawBody === '') {
            throw new InvalidJsonException();
        }

        $payload = json_decode($rawBody, true);

        if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException();
        }

        return $handler->handle($request->withParsedBody($payload));
    }

    private function shouldParseJson(ServerRequestInterface $request): bool
    {
        $method = strtoupper($request->getMethod());

        if (!in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            return false;
        }

        $contentType = $request->getHeaderLine('Content-Type');

        return str_contains($contentType, 'application/json');
    }
}