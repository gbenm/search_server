<?php

namespace Tests;

use App\Search\Domain\SearchEngine;
use App\Shared\Domain\CacheInterface;
use App\Stats\Domain\StatsRepository;
use DI\Container;
use Prophecy\Prophet;
use Slim\App;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\{Headers,Uri};
use Slim\Psr7\Request as SlimRequest;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ApiClient
{
    public $cacheProphecy;
    public $searchEngineProphecy;
    public $statsRepositoryProphecy;

    protected $initialized = false;

    public function __construct(
        public App $app,
        public Prophet $prophet,
    ) {
        $this->cacheProphecy = $this->prophet->prophesize(CacheInterface::class);
        $this->searchEngineProphecy = $this->prophet->prophesize(SearchEngine::class);
        $this->statsRepositoryProphecy = $this->prophet->prophesize(StatsRepository::class);
    }

    public function executeRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = [],
        array $body = [],
        array $query = [],
    ): Response {
        $this->setUp();

        $request = $this->createRequest(
            method: $method,
            path: $path,
            headers: $headers,
            cookies: $cookies,
            serverParams: $serverParams,
        );

        $request = $request->withQueryParams($query)->withParsedBody($body);

        return $this->app->handle($request);
    }

    public function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $this->setUp();

        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }

    protected function setUp()
    {
        if ($this->initialized) {
            return;
        }

        $this->setUpContainer();
    }

    protected function setUpContainer()
    {
      /** @var Container */
        $container = $this->app->getContainer();

        $cache = $this->cacheProphecy->reveal();
        $container->set(CacheInterface::class, $cache);
        $searchEngine = $this->searchEngineProphecy->reveal();
        $container->set(SearchEngine::class, $searchEngine);
        $statsRepository = $this->statsRepositoryProphecy->reveal();
        $container->set(StatsRepository::class, $statsRepository);
    }
}
