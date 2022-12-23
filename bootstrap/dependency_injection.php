<?php

use App\Search\Infrastructure\StackExchangeSearchEngine;
use App\Shared\Infrastructure\Database;
use App\Shared\Infrastructure\GuzzleClient;
use App\Shared\Infrastructure\RedisCache;
use App\Stats\Infrastructure\MySqlStatsRepository;
use DI\Container;

$container = new Container();

$container->set('cache', new RedisCache());

$http_client = new GuzzleClient();
$container->set('search_engine', new StackExchangeSearchEngine(
  client: $http_client,
));


$database = new Database();
$container->set('stats_repository', new MySqlStatsRepository(
  client: $database,
));

return $container;
