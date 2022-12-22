<?php

use App\Shared\Infrastructure\RedisCache;
use DI\Container;

$container = new Container();

$container->set('cache', new RedisCache());

return $container;
