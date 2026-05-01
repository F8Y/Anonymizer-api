<?php

declare(strict_types=1);

use App\Http\Middleware\ErrorHandlerMiddleware;
use App\Http\Middleware\JsonBodyMiddleware;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\App;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$rootPath = dirname(__DIR__);

if (file_exists($rootPath . '/.env')) {
    Dotenv::createImmutable($rootPath)->safeLoad();
}

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/container.php');

$container = $builder->build();

AppFactory::setContainer($container);

/** @var App $app */
$app = AppFactory::create();

(require __DIR__ . '/routes.php')($app);

/**
 * Middleware are executed in LIFO order.
 * ErrorHandlerMiddleware must be added last to become the outer layer
 * and catch errors from JsonBodyMiddleware and actions.
 */
$app->add($container->get(JsonBodyMiddleware::class));
$app->add($container->get(ErrorHandlerMiddleware::class));

return $app;