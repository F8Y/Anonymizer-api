<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/container.php');

$container = $builder->build();

AppFactory::setContainer($container);

/** @var App $app */
$app = AppFactory::create();

(require __DIR__ . '/routes.php')($app);

return $app;