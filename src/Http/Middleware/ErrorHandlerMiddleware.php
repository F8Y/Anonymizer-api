<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Exception\InvalidJsonException;
use App\Http\Response\ProblemDetailsResponseFactory;
use App\Support\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final readonly class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ProblemDetailsResponseFactory $problemDetailsResponseFactory
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (InvalidJsonException) {
            return $this->problemDetailsResponseFactory->invalidJson();
        } catch (ValidationException $exception) {
            return $this->problemDetailsResponseFactory->validation($exception);
        } catch (Throwable) {
            return $this->problemDetailsResponseFactory->internalServerError();
        }
    }
}