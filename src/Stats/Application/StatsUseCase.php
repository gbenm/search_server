<?php
namespace App\Stats\Application;

use App\Stats\Domain\Models\Stat;
use App\Stats\Domain\StatsRepository;
use DateTime;

class StatsUseCase
{
  public function __construct(
    private readonly StatsRepository $repo,
  ) {}

  /**
   * @return Stat[]
   */
  public function getMostSearched(int $top, DateTime $from, DateTime $until): array
  {
    return $this->repo->getMostSearched(
      top: $top,
      from: $from,
      until: $until,
    );
  }

  public function getStatsOf(string $query, bool $exact, int $count, DateTime $from, DateTime $until): array
  {
    return $this->repo->getStatsOf(
      query: $query,
      exact: $exact,
      count: $count,
      from: $from,
      until: $until,
    );
  }

  public function registerSearch(string $query): void
  {
    $this->repo->registerSearch($query);
  }
}
