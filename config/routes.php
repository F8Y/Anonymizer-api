<?php

declare(strict_types=1);

use App\Http\Action\AnonymizeAction;
use Slim\App;
use Slim\Psr7\Response;

return static function (App $app): void {
    $app->get('/health', function ($request, Response $response) {
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'service' => 'anonymizer-api',
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/v1/anonymize', AnonymizeAction::class);
};