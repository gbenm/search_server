<?php
namespace App\Shared\Domain;

interface HTTPClient {
  public function request(string $method, string $url, array $options = []): array;
}
