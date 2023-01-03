<?php

use App\Search\Domain\SearchEngine;
use App\Search\Infrastructure\StackExchangeSearchEngine;
use App\Shared\Domain\CacheInterface;
use App\Shared\Infrastructure\Database;
use App\Shared\Infrastructure\GuzzleClient;
use App\Shared\Infrastructure\RedisCache;
use App\Stats\Domain\StatsRepository;
use App\Stats\Infrastructure\MySqlStatsRepository;
use DI\Container;

$container = new Container();

$container->set(CacheInterface::class, new RedisCache());

$httpClient = new GuzzleClient();
$container->set(SearchEngine::class, new StackExchangeSearchEngine(
  client: $httpClient,
));


$database = new Database();
$container->set(StatsRepository::class, new MySqlStatsRepository(
  client: $database,
));

return $container;
