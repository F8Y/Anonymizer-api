<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../config/container.php');

$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

(require __DIR__ . '/../config/routes.php')($app);

$app->run();