<?php

declare(strict_types=1);

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

return $app;