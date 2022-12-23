<?php
namespace App\Shared\Infrastructure;

use App\Shared\Domain\HTTPClient;

class GuzzleClient implements HTTPClient
{
  public function request(string $method, string $url, array $options = []): array
  {
    $client = new \GuzzleHttp\Client();
    $response = $client->request($method, $url, $options);
    $body = $response->getBody()->getContents();
    $body = json_decode($body, associative: true);
    return $body;
  }
}
