<?php
namespace App\Shared\Infrastructure;

class Env {
  public static function getCacheTTL(): int
  {
    return (int) $_ENV['CACHE_TTL'] ?? 900;
  }
}
