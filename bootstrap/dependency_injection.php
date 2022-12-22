<?php

use App\Search\Infrastructure\StackExchangeSearchEngine;
use App\Shared\Infrastructure\RedisCache;
use DI\Container;

$container = new Container();

$container->set('cache', new RedisCache());
$container->set('search_engine', new StackExchangeSearchEngine());

return $container;
