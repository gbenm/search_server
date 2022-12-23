<?php
use Slim\Factory\AppFactory;

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/config.php';

$container = require __DIR__ . '/dependency_injection.php';

AppFactory::setContainer($container);
$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(
  displayErrorDetails: false,
  logErrors: true,
  logErrorDetails: true
);

$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

$routes = require_once __DIR__ . '/routes.php';
$routes($app);

return $app;
