<?php
namespace App\Search\Infrastructure;

use App\Search\Domain\Models\Result;
use App\Shared\Domain\HTTPClient;
use App\Shared\Domain\ServerError;
use Tests\Search\Factories\SearchProviderResponse;
use Tests\TestCase;

final class StackExchangeSearchEngineTest extends TestCase
{
  public function testCanSearch()
  {
    $clientProphecy = $this->prophet->prophesize(HTTPClient::class);
    $providerResponse = SearchProviderResponse::create(10);
    $clientProphecy->request(
      method: 'GET',
      url: 'https://api.fake.com/search',
      options: [
        'query' => [
          'intitle' => 'php',
          'site' => 'stackoverflow',
          'page' => 1,
          'pagesize' => 10,
        ],
      ],
    )->willReturn($providerResponse);

    $client = $clientProphecy->reveal();
    $searchEngine = new StackExchangeSearchEngine($client);
    $results = $searchEngine->search(
      query: 'php',
      page: 1,
      per_page: 10
    );

    $this->assertIsArray($results);
    $this->assertNotEmpty($results);
    $this->assertCount(10, $results);
    $this->assertInstanceOf(Result::class, $results[0]);

    $titlesMap = array_reduce(
      $providerResponse['items'],
      fn($acc, $result) => array_merge($acc, [$result['title'] => $result]),
      []
    );

    foreach ($results as $result) {
      $this->assertArrayHasKey($result->title, $titlesMap);
      $this->assertEquals($result->answer_count, $titlesMap[$result->title]['answer_count']);
      $this->assertEquals($result->username, $titlesMap[$result->title]['owner']['display_name']);
      $this->assertEquals($result->profile_picture_url, $titlesMap[$result->title]['owner']['profile_image']);
    }
  }

  public function testGetBadGatewayErrorIfProviderFails()
  {
    $clientProphecy = $this->prophet->prophesize(HTTPClient::class);
    $clientProphecy->request(
      method: 'GET',
      url: 'https://api.fake.com/search',
      options: [
        'query' => [
          'intitle' => 'php',
          'site' => 'stackoverflow',
          'page' => 26,
          'pagesize' => 10,
        ],
      ],
    )->willThrow(new ServerError(
      message: 'Bad Request',
      statusCode: 400,
      errorData: [
        'error_id' => 403,
        'error_message' => 'page above 25 requires access token or app key',
        'error_name' => 'access_denied',
      ],
    ));

    $client = $clientProphecy->reveal();
    $searchEngine = new StackExchangeSearchEngine($client);

    try {
      $searchEngine->search(
        query: 'php',
        page: 26,
        per_page: 10
      );
      $this->fail('Should throw ServerError');
    } catch (ServerError $error) {
      $this->assertEquals(502, $error->statusCode);
      $this->assertEquals('Bad Request', $error->getMessage());

      $providerError = $error->errorData['provider_error'];
      $this->assertEquals('access_denied', $providerError['error_name']);
      $this->assertEquals(
        'page above 25 requires access token or app key',
        $providerError['error_message']
      );
      $this->assertEquals(403, $providerError['error_id']);
    }
  }
}
