<?php
namespace App\Stats\Domain\Models;

final class Stat
{
  public function __construct(
    public readonly string $query,
    public readonly int $searches,
  ) {}

  public function toArray()
  {
    return [
      'query' => $this->query,
      'searches' => $this->searches,
    ];
  }
}
