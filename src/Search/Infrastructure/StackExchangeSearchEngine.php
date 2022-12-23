<?php
namespace App\Search\Infrastructure;

use App\Search\Domain\Models\Result;
use App\Search\Domain\SearchEngine;
use App\Shared\Domain\HTTPClient;
use App\Shared\Domain\ServerError;

class StackExchangeSearchEngine implements SearchEngine
{
  public function __construct(
    private readonly HTTPClient $client,
  ) {}

  public function search(string $query, int $page, int $per_page): array
  {
    if (empty($query)) {
      return [];
    }

    $results = [];

    try {
      $results = $this->client->request(
        method: 'GET',
        url: 'https://api.stackexchange.com/2.3/search',
        options: [
          'query' => [
            'intitle' => $query,
            'site' => 'stackoverflow',
            'page' => $page,
            'pagesize' => $per_page,
          ],
        ],
      );
    } catch (ServerError $e) {
      throw new ServerError(
        message: $e->getMessage(),
        statusCode: 502,
        errorData: [
          'provider_error' => $e->errorData,
        ],
      );
    }

    return array_map(
      fn(array $response) => $this->resultFromResponse($response),
      $results['items'],
    );
  }

  private function resultFromResponse(array $response): Result
  {
    return new Result(
      title: $response['title'],
      answer_count: $response['answer_count'],
      username: $response['owner']['display_name'],
      profile_picture_url: $response['owner']['profile_image'],
    );
  }
}
