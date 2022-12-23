<?php
namespace App\Shared\Infrastructure;

class Env {
  public static function getCacheTTL(): int
  {
    return (int) $_ENV['CACHE_TTL'] ?: 900;
  }

  public static function getRedisHost(): string
  {
    return $_ENV['REDIS_HOST'] ?: '127.0.0.1';
  }

  public static function getRedisPort(): int
  {
    return (int) $_ENV['REDIS_PORT'] ?: 6379;
  }
}
