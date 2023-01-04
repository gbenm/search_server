<?php
namespace Tests;

use Closure;
use DI\Container;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Prophecy\Prophet;
use Slim\App;
use Slim\Factory\AppFactory;

class TestCase extends FrameworkTestCase {
  protected Prophet $prophet;

  private Closure $setUpRoutes;

  public function __construct(?string $name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->setUpRoutes = require __DIR__ . '/../bootstrap/routes.php';
  }

  protected function createAppInstance(): App
  {
    $container = new Container();

    AppFactory::setContainer($container);
    $app = AppFactory::create();

    $this->setUpRoutes->call($this, $app);
    return $app;
  }

  protected function createApiClient (): ApiClient
  {
    return new ApiClient(
      app: $this->createAppInstance(),
      prophet: $this->prophet,
    );
  }

  protected function setUp(): void
  {
    $this->prophet = new Prophet;
  }

  protected function tearDown(): void
  {
    $this->prophet->checkPredictions();
  }
}