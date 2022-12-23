<?php
namespace App\Shared\Infrastructure;

use App\Shared\Domain\HTTPClient;
use App\Shared\Domain\ServerError;

class GuzzleClient implements HTTPClient
{
  public function request(string $method, string $url, array $options = []): array
  {
    try {
      $client = new \GuzzleHttp\Client();
      $response = $client->request($method, $url, $options);
      $body = $response->getBody()->getContents();
      $body = json_decode($body, associative: true);
      return $body;
    } catch (\GuzzleHttp\Exception\RequestException $e) {
      $response = $e->getResponse();
      $body = $response->getBody()->getContents();
      $body = json_decode($body, associative: true);
      throw new ServerError(
        message: $body['error_message'],
        statusCode: $response->getStatusCode(),
        errorData: $body,
      );
    }
  }
}
