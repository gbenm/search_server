<?php

namespace App\Shared\Infrastructure;

use Psr\Http\Message\ResponseInterface as Response;

trait Router
{
    public static function getFromContainer($container, $name)
    {
        return $container->get($name);
    }

    public static function asJson(Response $response)
    {
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function getCacheKeyFrom(string ...$parts)
    {
        return urlencode(implode(':', $parts));
    }
}
