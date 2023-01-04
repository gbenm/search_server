<?php
namespace Tests\Search\Infrastructure;

use Prophecy\Argument;
use Tests\ApiClient;
use Tests\Search\Factories\ResultFactory;
use Tests\TestCase;

final class SearchApi extends TestCase
{
  private function setUpCommonMockUpCalls(ApiClient $client)
  {
    $client->cacheProphecy->has(Argument::type('string'))->willReturn(false);
    $client->cacheProphecy->get(Argument::type('string'))->willReturn(null);
    $client->cacheProphecy->set(
      key: Argument::type('string'),
      value: Argument::type('string'),
      ttl: Argument::type('int')
    );

    $client->searchEngineProphecy->search(
      query: Argument::type('string'),
      page: Argument::type('int'),
      per_page: Argument::type('int')
    )->willReturn([]);
  }

  public function testCanSearchWithoutQuery()
  {
    $client = $this->createApiClient();
    $this->setUpCommonMockUpCalls($client);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/search'
    );

    $statusCode = $response->getStatusCode();

    $this->assertEquals(200, $statusCode);

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertEquals(0, count($responseData['data']['results']));
  }

  public function testCanSearchWithoutPagination()
  {
    $client = $this->createApiClient();
    $this->setUpCommonMockUpCalls($client);

    $expectedResult = ResultFactory::create();

    $client->searchEngineProphecy->search(
      query: 'php',
      page: 1,
      per_page: 10
    )->willReturn([$expectedResult]);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/search',
      query: [
        'query' => 'php'
      ]
    );

    $statusCode = $response->getStatusCode();

    $this->assertEquals(200, $statusCode);

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertEquals(1, count($responseData['data']['results']));

    $resultFound = $responseData['data']['results'][0];
    $this->assertEquals($expectedResult->title, $resultFound['title']);
    $this->assertEquals($expectedResult->answer_count, $resultFound['answer_count']);
    $this->assertEquals($expectedResult->username, $resultFound['username']);
    $this->assertEquals($expectedResult->profile_picture_url, $resultFound['profile_picture_url']);
  }

  public function testCanSearchWithPagination()
  {
    $client = $this->createApiClient();
    $this->setUpCommonMockUpCalls($client);

    $expectedResults = ResultFactory::createBatch(5);
    $resultsTitlesMap = array_reduce($expectedResults, function ($carry, $item) {
      $carry[$item->title] = $item;
      return $carry;
    }, []);

    $client->searchEngineProphecy->search(
      query: 'php',
      page: 10,
      per_page: 5
    )->willReturn($expectedResults);

    $response = $client->executeRequest(
      method: 'GET',
      path: '/search',
      query: [
        'query' => 'php',
        'page' => 10,
        'pagesize' => 5
      ]
    );

    $statusCode = $response->getStatusCode();

    $this->assertEquals(200, $statusCode);

    $responseData = json_decode($response->getBody(), true);
    $this->assertEquals('success', $responseData['status']);
    $this->assertEquals(5, count($responseData['data']['results']));

    foreach ($responseData['data']['results'] as $resultFound) {
      $this->assertTrue(isset($resultsTitlesMap[$resultFound['title']]));
    }
  }
}
