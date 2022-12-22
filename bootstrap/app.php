<?php

use Slim\Factory\AppFactory;
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

return $app;
