<?php

namespace App\Stats\Domain;

use App\Stats\Domain\Models\Stat;
use DateTime;

interface StatsRepository
{
  /**
   * @return Stat[]
   */
    public function getMostSearched(int $top, DateTime $from, DateTime $until): array;

  /**
   * @return Stat[]
   */
    public function getStatsOf(string $query, bool $exact, int $count, DateTime $from, DateTime $until): array;

    public function registerSearch(string $query): void;
}
