<?php
namespace App\Search\Infrastructure;

use App\Search\Domain\SearchEngine;

class StackExchangeSearchEngine implements SearchEngine {
  public function search(string $query, int $page, int $per_page): array
  {
    return [];
  }
}
