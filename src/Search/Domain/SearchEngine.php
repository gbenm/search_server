<?php

namespace App\Search\Domain;

use App\Search\Domain\Models\Result;

interface SearchEngine
{
  /**
   * @return Result[]
   */
    public function search(string $query, int $page, int $perPage): array;
}
