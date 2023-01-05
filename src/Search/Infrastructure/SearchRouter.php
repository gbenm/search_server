<?php

namespace App\Search\Infrastructure;

use App\Search\Application\SearchUseCase;
use App\Search\Domain\Models\Result;
use App\Search\Domain\SearchEngine;
use App\Shared\Domain\CacheInterface;
use App\Shared\Domain\ServerError;
use App\Shared\Infrastructure\Env;
use App\Shared\Infrastructure\Router;
use App\Stats\Application\StatsUseCase;
use App\Stats\Domain\StatsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

class SearchRouter
{
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

            $statsRepo = self::getFromContainer($this, StatsRepository::class);
            $statsUseCase = new StatsUseCase($statsRepo);
            $statsUseCase->registerSearch($query);

            /** @var CacheInterface */
            $cache = self::getFromContainer($this, CacheInterface::class);
            $cacheKey = self::getCacheKeyFrom('/search', $query, $page, $pagesize);
            $inCache = $cache->has($cacheKey);

            if ($inCache) {
                $cachedResults = $cache->get($cacheKey);
                $response->getBody()->write($cachedResults);
                return self::asJson($response);
            }

            $searchEngine = self::getFromContainer($this, SearchEngine::class);
            $searchUseCase = new SearchUseCase($searchEngine);

            $results = [];

            try {
                $results = $searchUseCase->search($query, $page, $pagesize);
            } catch (ServerError $e) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'data' => $e->errorData
                ]));

                return self::asJson($response->withStatus($e->statusCode));
            }

            $results = array_map(fn(Result $result) => $result->toArray(), $results);

            $jsonResponse = [
                'status' => 'success',
                'data' => [
                    'results' => $results,
                ]
            ];

            $jsonEncoded = json_encode($jsonResponse);
            $response->getBody()->write($jsonEncoded);

            $cache->set($cacheKey, $jsonEncoded, Env::getCacheTTL());

            return self::asJson($response);
        });
    }
}
