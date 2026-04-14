<?php

declare(stirct_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

$app->get('/health', function ($request, Response $response) {
    $response->getBody()->write(json_encode([
        'status' => 'ok',
        'service' => 'anonymizer-api',
    ], JSON_UNESCAPED_UNICODE));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/v1/anonymize', function ($request, Response $response) {
    $body = (string) $request->getBody();
    $payload = json_decode($body, true);

    $result = [
        'received' => $payload,
        'message' => 'TODO: Anonymization logic will be added later'
    ];

    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();