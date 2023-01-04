<?php
namespace App\Search\Infrastructure;

use App\Search\Domain\Models\Result;
use App\Shared\Domain\HTTPClient;
use Tests\Search\Factories\SearchProviderResponse;
use Tests\TestCase;

final class StackExchangeSearchEngineTest extends TestCase
{
  public function testCanSearch()
  {
    $_ENV['SEARCH_PROVIDER_URL'] = 'https://api.fake.com';
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
}
