<?php
namespace App\Search\Infrastructure;

use App\Search\Application\SearchUseCase;
use App\Search\Domain\Models\Result;
use App\Shared\Domain\CacheInterface;
use App\Shared\Infrastructure\Env;
use App\Shared\Infrastructure\Router;
use App\Stats\Application\StatsUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;


class SearchRouter {
  use Router;

  public static function setup(App $app)
  {
    self::searchEndpoint($app);
  }

  private static function searchEndpoint(App $app)
  {
    $app->get('/search', function (Request $request, Response $response, $args) {
      $query = $request->getQueryParams()['query'] ?? '';
      $page = $request->getQueryParams()['page'] ?? 1;
      $pagesize = $request->getQueryParams()['pagesize'] ?? 10;

      $statsRepo = self::getFromContainer($this, 'stats_repository');
      $stats_use_case = new StatsUseCase($statsRepo);
      $stats_use_case->registerSearch($query);

      /** @var CacheInterface */
      $cache = self::getFromContainer($this, 'cache');
      $cache_key = self::getCacheKeyFrom('/search', $query, $page, $pagesize);
      $in_cache = $cache->has($cache_key);

      if ($in_cache) {
        $cached_results = $cache->get($cache_key);
        $response->getBody()->write($cached_results);
        return self::asJson($response);
      }

      $search_engine = self::getFromContainer($this, 'search_engine');
      $search_use_case = new SearchUseCase($search_engine);

      $results = $search_use_case->search($query, $page, $pagesize);
      $results = array_map(fn(Result $result) => $result->toArray(), $results);

      $json_response = [
        'status' => 'success',
        'data' => [
          'results' => $results,
        ]
      ];

      $json_encoded = json_encode($json_response);
      $response->getBody()->write($json_encoded);

      $cache->set($cache_key, $json_encoded, Env::getCacheTTL());

      return self::asJson($response);
    });
  }
}
