<?php

declare(strict_types=1);

namespace Tests\Integration\Http;

use PHPUnit\Framework\TestCase;
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
        self::assertSame('ok', $payload['status']);
        self::assertSame('anonymizer-api', $payload['service']);
    }

    public function testAnonymizeEndpointReturnsAnonymizedData(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/v1/anonymize')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode([
            'full_name' => 'Иванов Иван Иванович',
            'email' => 'ivanov@example.com',
            'phone' => '+79991234567',
            'birth_date' => '2010-04-12',
        ], JSON_UNESCAPED_UNICODE));

        $request->getBody()->rewind();

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(200, $response->getStatusCode());
        self::assertMatchesRegularExpression('/^USER-[A-F0-9]{10}$/', $payload['full_name']);
        self::assertSame('i***@example.com', $payload['email']);
        self::assertSame('+7*******67', $payload['phone']);
        self::assertSame('2010', $payload['birth_date']);
    }

    public function testAnonymizeEndpointReturnsValidationErrorForInvalidEmail(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/v1/anonymize')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode([
            'full_name' => 'Иванов Иван Иванович',
            'email' => 'not-an-email',
            'phone' => '+79991234567',
            'birth_date' => '2010-04-12',
        ], JSON_UNESCAPED_UNICODE));

        $request->getBody()->rewind();

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('validation_error', $payload['error']['code']);
        self::assertArrayHasKey('email', $payload['error']['details']);
    }

    public function testAnonymizeEndpointReturnsValidationErrorForMissingFullName(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/v1/anonymize')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode([
            'email' => 'ivanov@example.com',
            'phone' => '+79991234567',
            'birth_date' => '2010-04-12',
        ], JSON_UNESCAPED_UNICODE));

        $request->getBody()->rewind();

        $response = $this->app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('validation_error', $payload['error']['code']);
        self::assertArrayHasKey('fullName', $payload['error']['details']);
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
        self::assertSame('invalid_json', $payload['error']['code']);
    }
}