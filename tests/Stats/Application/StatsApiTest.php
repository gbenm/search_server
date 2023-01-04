<?php
namespace Tests\Stats\Application;

use App\Stats\Domain\Models\Stat;
use DateTime;
use Prophecy\Argument;
use Tests\Stats\Factories\StatFactory;
use Tests\TestCase;

final class StatsApiTest extends TestCase
{
  public function testCanGetMostSearched()
  {
    $client = $this->createApiClient();

    $stats = StatFactory::createBatch(10);
    $client->statsRepositoryProphecy->getMostSearched(
      top: 10,
      from: Argument::type('DateTime'),
      until: Argument::type('DateTime')
    )->willReturn($stats);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/stats'
    );

    $this->assertEquals(200, $response->getStatusCode());

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertEquals(10, $responseData['data']['top']);
    $this->assertIsArray($responseData['data']['most_searched']);
    $this->assertCount(10, $responseData['data']['most_searched']);

    $queryMap = array_reduce($stats, function ($acc, Stat $stat) {
      $acc[$stat->query] = $stat;
      return $acc;
    }, []);

    $queries = array_map(fn ($stat) => $stat['query'], $responseData['data']['most_searched']);
    $uniqueQueries = array_unique($queries);
    $this->assertEquals(count($queries), count($uniqueQueries));

    foreach ($responseData['data']['most_searched'] as $stat) {
      /** @var Stat */
      $expected = $queryMap[$stat['query']];
      $this->assertEquals($expected->searches, $stat['searches']);
    }
  }

  public function testCanGetMostSearchedWithFixedTop()
  {
    $client = $this->createApiClient();

    $stats = StatFactory::createBatch(20);
    $client->statsRepositoryProphecy->getMostSearched(
      top: 20,
      from: Argument::type('DateTime'),
      until: Argument::type('DateTime')
    )->willReturn($stats);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/stats',
      query: [
        'top' => 20
      ]
    );

    $this->assertEquals(200, $response->getStatusCode());

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertEquals(20, $responseData['data']['top']);
    $this->assertIsArray($responseData['data']['most_searched']);
    $this->assertCount(20, $responseData['data']['most_searched']);
  }

  public function testCanGetMostSearchedWithDateRange()
  {
    $client = $this->createApiClient();

    $stats = StatFactory::createBatch(10);
    $client->statsRepositoryProphecy->getMostSearched(
      top: 10,
      from: new DateTime('2021-01-01'),
      until: new DateTime('2021-01-31')
    )->willReturn($stats);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/stats',
      query: [
        'from' => '2021-01-01',
        'until' => '2021-01-31'
      ]
    );

    $this->assertEquals(200, $response->getStatusCode());

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertEquals(10, $responseData['data']['top']);
    $this->assertIsArray($responseData['data']['most_searched']);
    $this->assertCount(10, $responseData['data']['most_searched']);
  }

  public function testCanGetStatsOfQuery()
  {
    $client = $this->createApiClient();

    $stats = StatFactory::createBatch(10);
    $client->statsRepositoryProphecy->getStatsOf(
      query: 'test',
      exact: false,
      count: 10,
      from: Argument::type('DateTime'),
      until: Argument::type('DateTime')
    )->willReturn($stats);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/stats/test'
    );

    $this->assertEquals(200, $response->getStatusCode());

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertIsArray($responseData['data']['stats']);
    $this->assertCount(10, $responseData['data']['stats']);

    $queryMap = array_reduce($stats, function ($acc, Stat $stat) {
      $acc[$stat->query] = $stat;
      return $acc;
    }, []);

    $queries = array_map(fn ($stat) => $stat['query'], $responseData['data']['stats']);
    $uniqueQueries = array_unique($queries);
    $this->assertEquals(count($queries), count($uniqueQueries));

    foreach ($responseData['data']['stats'] as $stat) {
      /** @var Stat */
      $expected = $queryMap[$stat['query']];
      $this->assertEquals($expected->searches, $stat['searches']);
    }
  }

  public function testCanGetStatsOfExactQuery()
  {
    $client = $this->createApiClient();

    $stat = StatFactory::create(query: 'test');
    $client->statsRepositoryProphecy->getStatsOf(
      query: 'test',
      exact: true,
      count: 10,
      from: Argument::type('DateTime'),
      until: Argument::type('DateTime')
    )->willReturn([$stat]);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/stats/test',
      query: [
        'exact' => 'true'
      ]
    );

    $this->assertEquals(200, $response->getStatusCode());

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertIsArray($responseData['data']['stats']);
    $this->assertCount(1, $responseData['data']['stats']);
  }

  public function testCanGetStatsOfQueryWithFixedCount()
  {
    $client = $this->createApiClient();

    $stats = StatFactory::createBatch(20);
    $client->statsRepositoryProphecy->getStatsOf(
      query: 'test',
      exact: false,
      count: 20,
      from: Argument::type('DateTime'),
      until: Argument::type('DateTime')
    )->willReturn($stats);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/stats/test',
      query: [
        'count' => 20
      ]
    );

    $this->assertEquals(200, $response->getStatusCode());

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertIsArray($responseData['data']['stats']);
    $this->assertCount(20, $responseData['data']['stats']);
  }

  public function testCanGetStatsOfQueryWithDateRange()
  {
    $client = $this->createApiClient();

    $stats = StatFactory::createBatch(10);
    $client->statsRepositoryProphecy->getStatsOf(
      query: 'test',
      exact: false,
      count: 10,
      from: new DateTime('2021-01-01'),
      until: new DateTime('2021-01-31')
    )->willReturn($stats);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/stats/test',
      query: [
        'from' => '2021-01-01',
        'until' => '2021-01-31'
      ]
    );

    $this->assertEquals(200, $response->getStatusCode());

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertIsArray($responseData['data']['stats']);
    $this->assertCount(10, $responseData['data']['stats']);
  }
}
