<?php

declare(strict_types=1);

namespace Tests\Integration\Http;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;

final class AnonymizeEndpointTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        /** @var App $app */
        $app = require __DIR__ . '/../../../config/app.php';

        $this->app = $app;
    }

    public function testHealthEndpointReturnsOk(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/health');

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json; charset=utf-8', $response->getHeaderLine('Content-Type'));
        self::assertSame('ok', $payload['status']);
        self::assertSame('anonymizer-api', $payload['service']);
    }

    public function testAnonymizeEndpointReturnsAnonymizedData(): void
    {
        $request = $this->jsonRequest('/v1/anonymize', [
            'login' => 'ivanov_ii',
            'first_middle_name' => 'Иван Иванович',
            'last_name' => 'Иванов',
            'email' => 'ivanov@example.com',
            'phone' => '+79991234567',
            'birth_date' => '2010-04-12',
        ]);

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));

        self::assertMatchesRegularExpression('/^USER-[A-F0-9]{12}$/', $payload['public_id']);
        self::assertMatchesRegularExpression('/^LOGIN-[A-F0-9]{12}$/', $payload['login']);
        self::assertSame('[обезличено]', $payload['first_middle_name']);
        self::assertSame('[обезличено]', $payload['last_name']);
        self::assertSame('i****v@***.com', $payload['email']);
        self::assertSame('+7********67', $payload['phone']);
        self::assertSame('2010', $payload['birth_date']);
    }

    public function testAnonymizeEndpointSupportsMissingOptionalFields(): void
    {
        $request = $this->jsonRequest('/v1/anonymize', [
            'login' => 'ivanov_ii',
            'first_middle_name' => 'Иван Иванович',
            'last_name' => 'Иванов',
            'email' => 'ivanov@example.com',
        ]);

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json; charset=utf-8', $response->getHeaderLine('Content-Type'));

        self::assertNull($payload['phone']);
        self::assertNull($payload['birth_date']);
    }

    public function testAnonymizeEndpointReturnsValidationErrorForInvalidEmail(): void
    {
        $request = $this->jsonRequest('/v1/anonymize', [
            'login' => 'ivanov_ii',
            'first_middle_name' => 'Иван Иванович',
            'last_name' => 'Иванов',
            'email' => 'not-an-email',
            'phone' => '+79991234567',
            'birth_date' => '2010-04-12',
        ]);

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('application/problem+json; charset=utf-8', $response->getHeaderLine('Content-Type'));
        self::assertSame('https://sirius27.local/problems/validation-error', $payload['type']);
        self::assertSame('Validation error', $payload['title']);
        self::assertSame(422, $payload['status']);
        self::assertSame('Request validation failed', $payload['detail']);
        self::assertArrayHasKey('email', $payload['errors']);
    }

    public function testAnonymizeEndpointReturnsValidationErrorForMissingLogin(): void
    {
        $request = $this->jsonRequest('/v1/anonymize', [
            'first_middle_name' => 'Иван Иванович',
            'last_name' => 'Иванов',
            'email' => 'ivanov@example.com',
            'phone' => '+79991234567',
            'birth_date' => '2010-04-12',
        ]);

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('application/problem+json; charset=utf-8', $response->getHeaderLine('Content-Type'));
        self::assertSame('https://sirius27.local/problems/validation-error', $payload['type']);
        self::assertSame('Validation error', $payload['title']);
        self::assertSame(422, $payload['status']);
        self::assertSame('Request validation failed', $payload['detail']);
        self::assertArrayHasKey('login', $payload['errors']);
    }

    public function testAnonymizeEndpointReturnsValidationErrorForMissingLastName(): void
    {
        $request = $this->jsonRequest('/v1/anonymize', [
            'login' => 'ivanov_ii',
            'first_middle_name' => 'Иван Иванович',
            'email' => 'ivanov@example.com',
            'phone' => '+79991234567',
            'birth_date' => '2010-04-12',
        ]);

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('application/problem+json; charset=utf-8', $response->getHeaderLine('Content-Type'));
        self::assertSame('https://sirius27.local/problems/validation-error', $payload['type']);
        self::assertSame('Validation error', $payload['title']);
        self::assertSame(422, $payload['status']);
        self::assertSame('Request validation failed', $payload['detail']);
        self::assertArrayHasKey('last_name', $payload['errors']);
    }

    public function testAnonymizeEndpointReturnsErrorForInvalidJson(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/v1/anonymize')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write('{invalid-json');
        $request->getBody()->rewind();

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('application/problem+json; charset=utf-8', $response->getHeaderLine('Content-Type'));
        self::assertSame('https://sirius27.local/problems/invalid-json', $payload['type']);
        self::assertSame('Invalid JSON', $payload['title']);
        self::assertSame(400, $payload['status']);
        self::assertSame('Request body must be valid JSON', $payload['detail']);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function jsonRequest(string $uri, array $payload): ServerRequestInterface
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', $uri)
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
        $request->getBody()->rewind();

        return $request;
    }
}