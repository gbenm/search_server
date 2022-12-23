<?php
namespace App\Stats\Infrastructure;

use App\Shared\Infrastructure\Env;
use App\Shared\Infrastructure\Router;
use App\Stats\Application\StatsUseCase;
use App\Stats\Domain\Models\Stat;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;


class StatsRouter {
  use Router;

  public static function setup(App $app)
  {
    self::mostSearchedEndpoint($app);
    self::queryStatsEndpoint($app);
  }

  private static function mostSearchedEndpoint(App $app)
  {
    $app->get('/stats', function (Request $request, Response $response, $args) {
      $request->getQueryParams();
      $params = $request->getQueryParams();

      $top = $params['top'] ?? 10;

      $from = self::getStartDateTime($params);
      $until = self::getEndDateTime($params);

      $statsRepo = self::getFromContainer($this, 'stats_repository');
      $stats_use_case = new StatsUseCase($statsRepo);
      $most_searched = $stats_use_case->getMostSearched(
        top: $top,
        from: $from,
        until: $until
      );

      $most_searched = array_map(fn(Stat $stat) => $stat->toArray(), $most_searched);

      $response->getBody()->write(json_encode([
        'status' => 'success',
        'data' => [
          'top' => $top,
          'most_searched' => $most_searched,
        ]
      ]));

      return self::asJson($response);
    });
  }

  private static function queryStatsEndpoint(App $app)
  {
    $app->get('/stats/{query}', function (Request $request, Response $response, $args) {
      $params = $request->getQueryParams();
      $query = $args['query'];

      $from = self::getStartDateTime($params);
      $until = self::getEndDateTime($params);
      $exact = $params['exact'] ?? 'false';
      $exact = $exact === 'true';
      $count = $params['count'] ?? 10;

      $statsRepo = self::getFromContainer($this, 'stats_repository');
      $stats_use_case = new StatsUseCase($statsRepo);
      $stats = $stats_use_case->getStatsOf(
        query: $query,
        count: $count,
        exact: $exact,
        from: $from,
        until: $until
      );

      $stats = array_map(fn(Stat $stat) => $stat->toArray(), $stats);

      $response->getBody()->write(json_encode([
        'status' => 'success',
        'data' => [
          'stats' => $stats,
        ]
      ]));

      return self::asJson($response);
    });
  }

  private static function getStartDateTime(array $params): DateTime
  {
    $from = $params['from'] ?? null;
    $from = $from ? new \DateTime($from) : Env::getDefaultStartDateTime();
    return $from;
  }

  private static function getEndDateTime(array $params): DateTime
  {
    $until = $params['until'] ?? null;
    $until = $until ? new \DateTime($until) : new DateTime();
    return $until;
  }
}
