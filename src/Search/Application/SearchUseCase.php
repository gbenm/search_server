<?php

namespace App\Search\Application;

use App\Search\Domain\Models\Result;
use App\Search\Domain\SearchEngine;

class SearchUseCase
{
    public function __construct(
        private readonly SearchEngine $engine,
    ) {
    }

    /**
     * @return Result[]
     */
    public function search(string $query, int $page, int $perPage): array
    {
        return $this->engine->search($query, $page, $perPage);
    }
}
