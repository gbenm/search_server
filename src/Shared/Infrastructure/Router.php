<?php
namespace App\Shared\Infrastructure;

use Psr\Http\Message\ResponseInterface as Response;

trait Router {

  static function getFromContainer($container, $name) {
    return $container->get($name);
  }

  static function asJson(Response $response) {
    return $response->withHeader('Content-Type', 'application/json');
  }

  static function getCacheKeyFrom(string ...$parts) {
    return urlencode(implode(':', $parts));
  }
}
